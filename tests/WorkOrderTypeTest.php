<?php

class WorkOrderTypeTest extends TestCase {

    /**
     * @testdox Work Order Types are identified by a name.
     * @test
     */
    public function basic() {
        $workOrderType = new \App\Model\WorkOrderType();
        $workOrderType->name = 'test';
        $workOrderType->save();
        $this->assertEquals('test', $workOrderType->name);
    }

}
