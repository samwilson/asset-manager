<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Model\Tag;

class TagsController extends Controller
{

    public function index()
    {
        $this->view->title = 'Tags';
        $this->view->tags = \App\Model\Tag::orderBy('name')->get();
        return $this->view;
    }

    public function json(Request $request)
    {
        $tags = Tag::where('name', 'LIKE', '%' . $request->input('term') . '%')->get();
        $out = array();
        foreach ($tags as $tag) {
            $out[] = array('label' => $tag->name);
        }
        return Response::json($out);
    }
}
