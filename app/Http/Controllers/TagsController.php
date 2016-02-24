<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Model\Tag;

class TagsController extends Controller
{

    public function index()
    {
        $this->view->breadcrumbs = [
            'tags' => 'Tags',
        ];
        $this->view->title = 'Tags';
        $this->view->tags = \App\Model\Tag::orderBy('name')->get();
        return $this->view;
    }

    public function save(Request $request)
    {
        if (!$this->user || !$this->user->isAdmin()) {
            $this->alert('info', trans('tags.not-allowed'));
            return redirect('tags');
        }
        if ($request->input('merge')) {
            $this->merge($request);
        }
        return redirect('tags');
    }

    protected function merge(Request $request)
    {
        $tagIds = $request->input('tags');
        $firstTag = Tag::find($tagIds[0]);
        if (!$firstTag) {
            return;
        }
        foreach ($tagIds as $tagId) {
            $tag = Tag::find($tagId);
            if (!$tag) {
                continue;
            }
            $firstTag->merge($tag);
        }
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
