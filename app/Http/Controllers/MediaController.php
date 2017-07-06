<?php

namespace App\Http\Controllers;

use App\Exceptions\YechefException;
use App\Models\Media;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Filesystem\Factory;

class MediaController extends Controller
{
	private $storage;

	public function __construct(Application $app, Factory $factory)
	{
		parent::__construct($app);

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

		$files = $request->file('file');
		foreach ($files as $file) {
			if (!$request->hasFile('file')) {
				throw new YechefException(13500);
			}

			if (!$file->isValid()) {
				throw new YechefException(13501);
			}

			$fileName = 'yechef_' . date('d-m-Y_H-i-s') . '.' . uniqid() . '.' . $file->getClientOriginalExtension();
			$uniqueFileName = md5($fileName . microtime());

			$s3 = $this->storage->disk('s3');

			// TODO: please leave it commented as it costs money to upload file to S3
			$s3->put($uniqueFileName, file_get_contents($file), 'public');

			$mimeType = $file->getClientMimeType();
			Log::info('mimetype is: ' . $mimeType);
			Log::info(env('AWS_URL') . $uniqueFileName);

			$fileUrl = $s3->url($uniqueFileName);
			$fileSize = $file->getClientSize();

			$validationRule = Media::getValidationRule();
			$this->validateInput($request, $validationRule);

			// Save to local db
			Media::create([
				'slug'          => snake_case($uniqueFileName),
				'url'           => env('AWS_URL') . $uniqueFileName,
				// not sure if there is an exact method to determine image or video
				'type'          => 'image',
				'mediable_id'   => $mediable_id,
				'mediable_type' => $mediable_type
			]);

		}

		return response()->success(13000);

	}

}
