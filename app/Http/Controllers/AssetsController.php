<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Asset;
use App\Model\Category;
use App\Model\Tag;

class AssetsController extends Controller {

    public function index(Request $request) {

        // Get search terms.
        $assetIdentifiers = preg_split('/(\n|\r)/', $request->input('identifiers', ''), NULL, PREG_SPLIT_NO_EMPTY);
        $this->view->identifiers = array_map('trim', $assetIdentifiers);
        $this->view->identifier = trim($request->input('identifier'));
        $this->view->tagged = $request->input('tagged');
        $this->view->not_tagged = $request->input('not_tagged');
        $this->view->categoryIds = collect($request->input('category_ids'));

        // No assets at all?
        if (Asset::count() === 0) {
            $this->alert('info', trans('app.no-assets-yet'), false);
        }

        // Build and execute query.
        if ($request->input('search')) {
            $assets = Asset::query();
            if (!empty($this->view->identifiers)) {
                $assets->whereIn('identifier', $this->view->identifiers);
            }
            if (!empty($this->view->identifier)) {
                $assets->where('identifier', 'LIKE', '%' . $this->view->identifier . '%');
            }
            if ($this->view->tagged) {
                $assets->tagged($this->view->tagged);
            }
            if ($this->view->categoryIds->count() > 0) {
                $assets->whereHas('categories', function ($query) {
                    $query->whereIn('id', $this->view->categoryIds);
                })->get();
            }
            $this->view->assets = $assets->paginate(50);
            if ($this->view->assets->total() === 0) {
                $this->alert('success', 'No assets found with the given criteria.', false);
            }
        }

        // Add extra view data, and return.
        $this->view->categories = Category::where('parent_id', NULL)->orderBy('name', 'ASC')->get();
        return $this->view;
    }

    public function import() {
        $this->view->title = 'Import';
        $this->view->breadcrumbs = [
            'assets' => 'Assets',
            'assets/import' => 'Import',
        ];
        return $this->view;
    }

    public function importPost(Request $request) {
        if (!$this->user->isAdmin()) {
            $this->alert('info', 'Only admins can import.');
            return redirect('/assets/import');
        }
        if (empty($_FILES['file']['tmp_name'])) {
            return redirect('/assets/import');
        }

        $fileInfo = $_FILES['file'];
        $csv = new \App\Csv($fileInfo['tmp_name']);
        if (!$csv->has_header('Identifier')) {
            throw new \Exception("'Identifier' column missing from CSV.");
        }
        $tagIds = Tag::getIds($request->input('tags'));
        while ($csv->next()) {
            $identifier = $csv->get('Identifier');
            $asset = Asset::firstOrCreate(['identifier' => $identifier]);
            $asset->tags()->attach($tagIds);
        }
        return redirect('/assets');
    }

    public function view($id) {
        $asset = \App\Model\Asset::where('id', $id)->first();
        $this->view->title = $asset->identifier;
        $this->view->breadcrumbs = [
            'assets' => 'Assets',
            'assets/' . $asset->id => $asset->identifier,
        ];
        $this->view->asset = $asset;
        return $this->view;
    }

    public function edit($id) {
        if (!$this->user || !$this->user->isClerk()) {
            $this->alert('warning', 'Only Clerks are allowed to edit Assets.');
            return redirect("assets/$id");
        }
        $this->view->asset = Asset::where('id', $id)->first();
        $this->view->categories = Category::where('parent_id', NULL)->orderBy('name', 'ASC')->get();
        $this->view->breadcrumbs = [
            'assets' => 'Assets',
            'assets/' . $this->view->asset->id => $this->view->asset->identifier,
            'assets/' . $this->view->asset->id . '/edit' => 'Edit',
        ];
        $this->view->selectedCategories = $this->view->asset->categories;
        return $this->view;
    }

    public function create() {
        if (!$this->user || !$this->user->isClerk()) {
            $this->alert('info', 'Only Managers can create assets.');
            return redirect('/assets');
        }
        $this->view->title = 'Create Asset';
        $this->view->breadcrumbs = [
            'assets' => 'Assets',
            'assets/create' => 'Create',
        ];
        $this->view->categories = Category::where('parent_id', NULL)->orderBy('name', 'ASC')->get();
        $this->view->asset = new Asset();
        return $this->view;
    }

    public function save(Request $request) {
        if (!$this->user || !$this->user->isClerk()) {
            $this->alert('warning', 'Only Clerks are allowed to edit assets.', false);
            return $this->view;
        }
        $asset = Asset::firstOrNew(['id' => $request->input('id')]);
        $asset->identifier = $request->input('identifier');
        $asset->comments = $request->input('comments');
        $asset->save();
        $asset->tags()->sync(Tag::getIds($request->input('tags')));
        $asset->categories()->sync($request->input('category_ids', array()));
        return redirect('assets/' . $asset->id);
    }

}
