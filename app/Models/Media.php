<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    public $guarded = ["id"];
    public function mediaable()
    {
        return $this->morphTo();
    }
    public static function upload_media($mediable, $file, $path)
    {
        if (!$file || !$file->isValid()) {
            throw new \Exception("Invalid file upload.");
        }
        $extension = $file->extension();
        $type = $file->getMimeType();
        $size = $file->getSize() / 1024;
        $storedPath = $file->store($path, 'public');
        $res = $mediable->media()->create(['storage' => 'local', 'file' => $storedPath, 'extension' => $extension, 'file_type' => $type, 'size' => $size]);
        if (!$res) {
            throw new \Exception('Media Upload Fail');
        }
    }
}
