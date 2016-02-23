<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Asset;

class SearchController extends Controller
{

    public function search(Request $request)
    {
        $q = $request->query('q');
        switch ($request->query('type')) {
            case 'a':
                $asset = Asset::whereIdentifier($q)->first();
                if (isset($asset->id)) {
                    return redirect('assets/' . $asset->id);
                } else {
                    return redirect("assets?identifier=$q");
                }
                break;
        }
        $this->view->q = $q;
        return $this->view;
    }
}
