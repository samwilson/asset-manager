<?php

use App\Model\Job;
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

    /**
     * @testdox A JobList has a list of attached Jobs.
     */
    public function jobs() {
        $type = new \App\Model\JobType();
        $type->save();
        $jobList = new JobList();
        $jobList->type_id = $type->id;
        $jobList->save();

        // One incomplete job.
        $job = new Job();
        $job->job_list_id = $jobList->id;
        $job->save();
        $this->assertEquals(1, $jobList->jobs->count());
        $this->assertEquals(0, $jobList->percentComplete());
        $this->assertEquals(100, $jobList->percentIncomplete());

        // Two incomplete jobs.
        $job2 = new Job();
        $job2->job_list_id = $jobList->id;
        $job2->save();
        $jobList->load('jobs');
        $this->assertEquals(2, $jobList->jobs->count());
        $this->assertEquals(0, $jobList->percentComplete());
        $this->assertEquals(100, $jobList->percentIncomplete());

        // Complete one of them.
        $job->date_resolved = '2015-09-09';
        $job->save();
        $this->assertEquals(50, $jobList->percentIncomplete());
    }

}
