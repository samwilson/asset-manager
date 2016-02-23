<?php

namespace App\Model;

use \Symfony\Component\HttpFoundation\File\UploadedFile;

class File extends Model
{

    const FORMAT_ORIGINAL = 'o';
    const FORMAT_THUMB = 't';
    const FORMAT_DISPLAY = 'd';

    public function asset()
    {
        return $this->belongsTo('App\\Model\\Asset', 'asset_id');
    }

    /**
     * Save an uploaded file into the standard system.
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $uploadedFile
     * @return File|false
     */
    public static function createFromUploaded($uploadedFile)
    {
        if (!$uploadedFile instanceof UploadedFile) {
            return false;
        }
        $file = new File();
        $file->name = $uploadedFile->getClientOriginalName();
        $file->mime_type = $uploadedFile->getMimeType();
        $file->save();
        $uploadedFile->move(dirname($file->getPathname()), basename($file->getPathname()));
        return $file;
    }

    public function makeSmallerSize($format)
    {
        if (!$this->isImage()) {
            return false;
        }
        $resizedPathname = $this->getPathname($format);
        $img = \Image::make($this->getPathname(self::FORMAT_ORIGINAL));
        // Thumbnails.
        if ($format === self::FORMAT_THUMB) {
            $img->resize(200, 200, function ($constraint) {
                $constraint->upsize();
            });
        }
        // Display size.
        if ($format === self::FORMAT_DISPLAY) {
            $img->resize(null, 600, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }
        // Save the image.
        if (!is_dir(dirname($resizedPathname))) {
            mkdir(dirname($resizedPathname), null, true);
        }
        $img->save($resizedPathname);
    }

    public function getPathname($format = null)
    {
        if (!$format) {
            $format = self::FORMAT_ORIGINAL;
        }
        if (!$this->id) {
            throw new \Exception("File record must be loaded before requesting pathname");
        }
        $hash = md5($this->id);
        $path = 'files/' . $format . '/' . $hash[0] . $hash[1] . '/' . $hash[2] . $hash[3] . '/' . $this->id;
        return storage_path($path);
    }

    /**
     * Whether this file is an image or not.
     */
    public function isImage()
    {
        return strpos($this->mime_type, 'image') === 0;
    }

    /**
     * Create thumbnail and display sizes.
     */
    public function buildOtherSizes()
    {
        
    }
}
