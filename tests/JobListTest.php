<?php

use App\Model\JobList;

class JobListTest extends TestCase {

    /**
     * @testdox A Job List is a collection of Assets that must have some Tasks performed on them.
     * @test
     */
    public function basic() {
        $jobList = new JobList();
        $this->assertEquals(0, $jobList->jobs->count());
    }

    /**
     * @testdox It is possible to retrieve a list of the Assets in the Work Order that have not yet had their Contacts contacted.
     * @test
     */
    public function y() {
        
    }

}
