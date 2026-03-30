<?php

/*
|--------------------------------------------------------------------------
| Ship Helpers
|--------------------------------------------------------------------------
|
| Here is where you can register all the global helpers.
| Write Container specific helpers in the respective Container.
| All the helpers added here and in the Containers will be loaded automatically.
|
*/

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

if (!function_exists('upload_to_gcs')) {
    /**
     * Upload file to GCS and return the correct public URL.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $folder
     * @return string|null
     */
    function upload_to_gcs($file, string $folder = 'uploads'): ?string
    {
        if ($file instanceof UploadedFile && !$file->isValid()) {
            throw new \Exception("File upload failed before saving to GCS: " . $file->getErrorMessage());
        }

        $path = Storage::disk('gcs')->putFile($folder, $file);
        if (!$path) {
            throw new \Exception("Storage::disk('gcs')->putFile() failed and returned false. Check GCS permissions or file stream visibility.");
        }

        $bucket = config('filesystems.disks.gcs.bucket');
        return "https://storage.googleapis.com/{$bucket}/" . ltrim($path, '/');
    }
}
