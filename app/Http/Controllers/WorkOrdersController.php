<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Asset;
use App\Model\WorkOrder;

class WorkOrdersController extends Controller {

    public function create(Request $request) {
        $this->view->assets = $this->getAssets($request);
        $this->view->title = "Create a Work Order";
        $this->view->breadcrumbs = [
            'work-orders' => 'Work Orders',
            'assets' => 'Create',
        ];
        $this->view->work_order_types = \App\Model\WorkOrderType::orderBy('name', 'ASC')->get();
        return $this->view;
    }

    public function save(Request $request) {
        $workOrder = new WorkOrder();
        $workOrder->name = $request->input('name');
        $workOrder->work_order_type_id = $request->input('work_order_type_id');
        $workOrder->save();

        $assets = $this->getAssets($request);
        $assetIds = array();
        foreach ($assets as $asset) {
            $assetIds[] = $asset->id;
        }
        $workOrder->assets()->sync($assetIds);

        return redirect('work-orders/' . $workOrder->id);
    }

    public function view(Request $request, $id) {
        $workOrder = WorkOrder::find($id);
        $this->view->work_order = $workOrder;
        $this->view->assets = $workOrder->assets->all();
        $this->view->title = "Work Order " . $workOrder->name;
        $this->view->breadcrumbs = [
            'work-orders' => 'Work Orders',
            'work-orders/'.$workOrder->id => $workOrder->name,
        ];
        return $this->view;
    }

    protected function getAssets($request) {
        // Get search terms.
        $assetIdentifiers = preg_split('/(\n|\r)/', $request->input('identifiers', ''), NULL, PREG_SPLIT_NO_EMPTY);
        $identifiers = array_map('trim', $assetIdentifiers);
        $identifier = trim($request->input('identifier'));
        $categoryIds = collect($request->input('category_ids'));

        // Build and execute query.
        $assets = Asset::query();
        if (!empty($identifiers)) {
            $assets->whereIn('identifier', $identifiers);
        }
        if (!empty($identifier)) {
            $assets->where('identifier', 'LIKE', "%$identifier%");
        }
        if ($categoryIds->count() > 0) {
            $assets->whereHas('categories', function ($query) {
                $query->whereIn('id', $categoryIds);
            })->get();
        }
        return $assets->get();
    }

}
