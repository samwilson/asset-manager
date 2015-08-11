<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Crew;
use App\Model\WorkOrder;
use App\Model\ScheduledWorkOrder;

class ScheduledWorkOrdersController extends Controller {

    public function index(Request $reqest) {
        $this->view->title = "Work Order Schedule";
        $this->view->breadcrumbs = [
            'work-orders' => 'Work Orders',
            'work-orders/schedule' => 'Schedule',
        ];
        $startDate = new \DateTime($reqest->input('start_date'));
        $endDate = new \DateTime($reqest->input('end_date'));
        if ($startDate->diff($endDate, true)->d < 7) {
            $endDate->add(new \DateInterval('P14D'));
        }
        $datePeriod = new \DatePeriod($startDate, new \DateInterval('P1D'), $endDate);
        $this->view->dates = $datePeriod;
        $this->view->crews = Crew::query()->orderBy('name')->get();
        return $this->view;
    }

    public function crew($crewId) {
        $crew = Crew::find($crewId);
        $this->view->scheduled_work_orders = $crew->scheduledWorkOrders()->current()->get();
        return $this->view;
    }

    public function form(Request $request, $workOrderId, $scheduledWoId = null) {
        $this->view->crews = Crew::orderBy('name', 'ASC')->get();
        $workOrder = WorkOrder::find($workOrderId);
        $this->view->work_order = $workOrder;
        $this->view->swo = ScheduledWorkOrder::find($scheduledWoId);
        $this->view->breadcrumbs = [
            'work-orders' => 'Work Orders',
            'work-orders/' . $workOrder->id => $workOrder->name,
            'work-orders/' . $workOrder->id . '/schedule' => 'Schedule',
        ];
        $this->view->title = 'Schedule Work Order "' . $workOrder->name . '"';
        return $this->view;
    }

    public function save(Request $request, $workOrderId) {
        $swo = ScheduledWorkOrder::findOrNew($request->input('id'));
        if ($swo->work_order_id === null) {
            $swo->work_order_id = $workOrderId;
        }
        $swo->crew_id = $request->input('crew_id');
        $swo->start_date = $request->input('start_date');
        $swo->end_date = $request->input('end_date');
        $swo->save();
        return redirect('work-orders/' . $swo->workOrder->id);
    }

}
