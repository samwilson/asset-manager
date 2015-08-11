<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Asset;
use App\Model\WorkOrder;
use App\Model\WorkOrderType;

class WorkOrdersController extends Controller {

    public function index(Request $request) {
        $this->view->title = 'Work Orders';
        $this->view->breadcrumbs = [
            'work-orders' => 'Work Orders',
        ];
        $this->view->work_order_types = WorkOrderType::orderBy('name', 'ASC')->get();
        $workOrders = WorkOrder::query();

        // Type.
        $this->view->selected_type = $request->input('type');
        if ($this->view->selected_type !== null) {
            $workOrders->where('work_order_type_id', $this->view->selected_type);
        }

        $this->view->work_orders = $workOrders->paginate(50);
        return $this->view;
    }

    public function create(Request $request) {
        $this->view->assets = $this->getAssets($request);
        $this->view->title = "Create a Work Order";
        $this->view->breadcrumbs = [
            'work-orders' => 'Work Orders',
            'assets' => 'Create',
        ];
        $this->view->work_order_types = WorkOrderType::orderBy('name', 'ASC')->get();
        return $this->view;
    }

    public function edit($id) {
        $this->view->work_order = WorkOrder::find($id);
        $this->view->title = 'Edit Work Order #'.$this->view->work_order->id;
        $this->view->work_order_types = WorkOrderType::orderBy('name', 'ASC')->get();
        $this->view->breadcrumbs = [
            'work-orders' => 'Work Orders',
            'work-orders/' . $this->view->work_order->id => $this->view->work_order->name,
            'work-orders/' . $this->view->work_order->id . '/edit' => 'Edit',
        ];
        return $this->view;
    }

    public function saveNew(Request $request) {
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

    public function saveExisting(Request $request, $id) {
        $workOrder = WorkOrder::find($id);
        $workOrder->name = $request->input('name');
        $workOrder->work_order_type_id = $request->input('work_order_type_id');
        $workOrder->due_date = $request->input('due_date');
        $workOrder->save();
        return redirect('work-orders/' . $workOrder->id);
    }

    public function view(Request $request, $id) {
        $workOrder = WorkOrder::with('type')->find($id);
        $this->view->work_order = $workOrder;
        $this->view->assets = $workOrder->assets->all();
        $this->view->schedules = $workOrder->schedules()->orderBy('start_date', 'DESC')->get();
        $this->view->title = "Work Order " . $workOrder->name;
        $this->view->breadcrumbs = [
            'work-orders' => 'Work Orders',
            'work-orders/' . $workOrder->id => $workOrder->name,
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
