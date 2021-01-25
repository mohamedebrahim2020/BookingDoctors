<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

trait StoreFileTrait 
{
public function addFileToPublic($file, $folder)
    {
        $fileName   = time() . '.' . $file->getClientOriginalExtension();
        $img = Image::make($file->getRealPath());
        $randomString = Str::random(15);
        $fileNametostore = $folder . '/'. $randomString . $fileName;
        Storage::put($fileNametostore, $img);
        return $fileNametostore;
    }
}    