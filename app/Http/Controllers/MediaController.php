<?php

namespace App\Http\Controllers;

use App\Exceptions\YechefException;
use App\Models\Kitchen;
use App\Models\Media;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
	private $validator;

	/**
	 * KitchenController constructor.
	 * @param Application $app
	 */
	public function __construct(Application $app)
	{
		$this->validator = $app->make('validator');
	}

	/**
	 * @param Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		$mediable_id = $request->input('mediable_id');
		$mediable_type = $request->input('mediable_type');
		Log::info($mediable_id);
		Log::info($mediable_type);

		$files = $request->file('file');
		foreach ($files as $file) {
			if (!$request->hasFile('file')) {
				throw new YechefException(13500);
			}

			if (!$file->isValid()) {
				throw new YechefException(13501);
			}

			$fileName = 'yechef_' . uniqid() . '.' . $file->getClientOriginalExtension();
			$s3 = Storage::disk('s3');
			// please leave it commented as it costs money to upload file to S3
//			$s3->put($fileName, file_get_contents($file), 'public');
			$mimeType = $file->getClientMimeType();
			Log::info('mimetype is: ' . $mimeType);
			Log::info(env('AWS_URL') . $fileName);

			$fileUrl = $s3->url($fileName);
			$fileSize = $file->getClientSize();

			$this->validateInput($request);
			Log::info('validated');

			// Save to local db
			$media = Media::create([
				'slug'          => snake_case($fileName),
				'url'           => env('AWS_URL') . $fileName,
				// not sure if there is an exact method to determine image or video
				'type'          => 'image',
				'mediable_id'   => $mediable_id,
				'mediable_type' => $mediable_type
			]);

			Log::info('media created');

		}

		return response()->success();

	}

	private function validateInput(Request $request)
	{
		$validationRule = Media::getValidationRule();
		$validator = $this->validator->make($request->all(), $validationRule);

		if ($validator->fails()) {
			throw new YechefException(13502);
		}
	}

}
