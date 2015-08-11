<?php

use App\Model\WorkOrder;

class WorkOrderTest extends TestCase {

    /**
     * @testdox A Work Order is a collection of Assets that must have a particular Job performed on them.
     * @test
     */
    public function basic() {
        $workOrder = new WorkOrder();
        $this->assertEquals(0, $workOrder->assets->count());
    }

    /**
     * @testdox It is possible to retrieve a list of the Assets in the Work Order that have not yet had their Contacts contacted.
     * @test
     */
    public function y() {
        
    }

}
