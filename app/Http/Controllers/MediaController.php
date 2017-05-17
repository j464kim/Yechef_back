<?php

namespace App\Http\Controllers;

use App\Exceptions\YechefException;
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
		if (!$request->hasFile('file')) {
			throw new YechefException(13500);
		}

		if (!$request->file('file')->isValid()) {
			throw new YechefException(13501);
		}

		// retrieve file from request
		$file = $request->file('file');

		$fileName = 'yechef_' . uniqid() . '.' . $file->getClientOriginalExtension();
		$s3 = Storage::disk('s3');
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
			'slug' => snake_case($fileName),
			'url'  => env('AWS_URL') . $fileName,
			// not sure if there is an exact method to determine image or video
			'type' => 'image'
		]);

		Log::info('media created');
		return response()->success($media);

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
