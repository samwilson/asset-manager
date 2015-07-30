<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AssetsController extends Controller {

    public function index() {
        $view = $this->getView('assets/index');
        $view->assets = \App\Model\Asset::all();
        return $view;
    }

    public function import() {
        $view = $this->getView('assets/import');
        return $view;
    }

    public function view($id) {
        $asset = \App\Model\Asset::where('id', $id)->first();
        $view = $this->getView('assets.view');
        $view->asset = $asset;
        return $view;
    }

    public function edit($id) {
        $view = $this->getView('assets.edit');
        $view->asset = \App\Model\Asset::where('id', $id)->first();
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
