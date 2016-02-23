<?php

namespace App\Http\Controllers;

use App\Model\File;

class FilesController extends Controller
{

    public function view($id, $format = null)
    {
        $file = File::findOrFail($id);
        $pathname = $file->getPathname($format);
        if (!file_exists($pathname)) {
            $file->makeSmallerSize($format);
        }
        if (!file_exists($pathname)) {
            abort(404);
        }
        return response()->download($pathname, $file->name, [], 'inline');
    }
}
