<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class File extends Model
{

    protected $guarded = false;
    protected $table = 'files';

    public static function putAndCreate(UploadedFile $dataFile)
    : File {
        $path = Storage::disk('public')->put('files/', $dataFile);
        return File::create([
            'path'      => $path,
            'mime_type' => $dataFile->getClientOriginalExtension(),
            'title'     => $dataFile->getClientOriginalName(),
        ]);
    }

}
