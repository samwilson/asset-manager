<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Asset;
use App\Model\AssetCategory;

class AssetsController extends Controller {

    public function index(Request $request) {
        $view = $this->getView('assets/index');

        // Get search terms.
        $assetIdentifiers = preg_split('/(\n|\r)/', $request->input('identifiers', ''), NULL, PREG_SPLIT_NO_EMPTY);
        $view->identifiers = array_map('trim', $assetIdentifiers);

        // Build and execute query.
        $assets = Asset::query();
        if (!empty($view->identifiers)) {
            $assets->whereIn('identifier', $view->identifiers);
        }

        // Add extra view data, and return.
        $view->assets = $assets->paginate(50);
        $view->categories = AssetCategory::where('parent_id', NULL)->orderBy('name', 'ASC')->get();
        return $view;
    }

    public function import() {
        $view = $this->getView('assets/import');
        return $view;
    }

    public function view($id) {
        $asset = \App\Model\Asset::where('id', $id)->first();
        $view = $this->getView('assets.view');
        $view->title = $asset->identifier;
        $view->breadcrumbs = [
            'assets' => 'Assets',
            'assets/'.$asset->id => $asset->identifier,
        ];
        $view->asset = $asset;
        return $view;
    }

    public function edit($id) {
        $view = $this->getView('assets.edit');
        $view->asset = \App\Model\Asset::where('id', $id)->first();
        $view->categories = AssetCategory::where('parent_id', NULL)->orderBy('name', 'ASC')->get();
        $view->breadcrumbs = [
            'assets' => 'Assets',
            'assets/'.$view->asset->id => $view->asset->identifier,
            'assets/'.$view->asset->id.'/edit' => 'Edit',
        ];
        return $view;
    }

    public function create() {
        $view = $this->getView('assets.edit');
        $view->asset = new \App\Model\Asset();
        return $view;
    }

    public function save(Request $request) {
        $asset = \App\Model\Asset::firstOrNew(['id' => $request->input('id')]);
        $asset->identifier = $request->input('identifier');
        $asset->save();
        return redirect('assets/' . $asset->id);
    }

}
