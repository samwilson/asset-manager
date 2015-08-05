<?php

namespace App\Http\Controllers;

class AssetCategoriesController extends \App\Http\Controllers\Controller {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        $view = $this->getView('asset-categories/admin');
        $view->asset_categories = \App\Model\AssetCategory::get();
        return $view;
    }

}
