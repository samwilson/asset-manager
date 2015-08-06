<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Asset;
use App\Model\Category;

class AssetsController extends Controller {

    public function index(Request $request) {

        // Get search terms.
        $assetIdentifiers = preg_split('/(\n|\r)/', $request->input('identifiers', ''), NULL, PREG_SPLIT_NO_EMPTY);
        $this->view->identifiers = array_map('trim', $assetIdentifiers);
        $this->view->identifier = trim($request->input('identifier'));
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
                $assets->where('identifier', 'LIKE', '%'.$this->view->identifier.'%');
            }
            if ($this->view->categoryIds->count() > 0) {
                $assets->whereHas('categories', function ($query) {
                    $query->whereIn('id', $this->view->categoryIds);
                })->get();
            }
            $this->view->assets = $assets->paginate(50);
        }

        // Add extra view data, and return.
        $this->view->categories = Category::where('parent_id', NULL)->orderBy('name', 'ASC')->get();
        return $this->view;
    }

    public function import() {
        $view = $this->getView('assets/import');
        return $view;
    }

    public function view($id) {
        $asset = \App\Model\Asset::where('id', $id)->first();
        $this->view->title = $asset->identifier;
        $this->view->breadcrumbs = [
            'assets' => 'Assets',
            'assets/'.$asset->id => $asset->identifier,
        ];
        $this->view->asset = $asset;
        return $this->view;
    }

    public function edit($id) {
        $this->view->asset = Asset::where('id', $id)->first();
        $this->view->categories = Category::where('parent_id', NULL)->orderBy('name', 'ASC')->get();
        $this->view->breadcrumbs = [
            'assets' => 'Assets',
            'assets/'.$this->view->asset->id => $this->view->asset->identifier,
            'assets/'.$this->view->asset->id.'/edit' => 'Edit',
        ];
        $this->view->selectedCategories = $this->view->asset->categories;
        return $this->view;
    }

    public function create() {
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
        $asset = Asset::firstOrNew(['id' => $request->input('id')]);
        $asset->identifier = $request->input('identifier');
        $asset->save();
        $asset->categories()->sync($request->input('category_ids', array()));
//        foreach ($request->input('category_ids', array()) as $catId) {
//            $asset->categories()->attach($catId);
//        }
        return redirect('assets/' . $asset->id);
    }

}
