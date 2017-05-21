<?php

namespace App\Http\Controllers;

use App\Exceptions\YechefException;
use App\Models\Media;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Filesystem\Factory;

class MediaController extends Controller
{
	private $validator;
	private $storage;

	/**
	 * KitchenController constructor.
	 * @param Application $app
	 */
	public function __construct(Application $app, Factory $factory)
	{
		$this->validator = $app->make('validator');
		$this->storage = $factory;
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

			$path_parts = pathinfo($_FILES["p_image"]["name"]);
			$fileName = 'yechef_' . date('d-m-Y_H-i-s') . '.' . uniqid() . '.' . $file->getClientOriginalExtension();
			$s3 = $this->storage->disk('s3');

			// please leave it commented ads it costs money to upload file to S3
			$s3->put($fileName, file_get_contents($file), 'public');

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

		return response()->success(13000);

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
