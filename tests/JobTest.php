<?php

use App\Model\Job;
use App\Model\JobList;

class JobTest extends TestCase {

    /**
     * @testdox A Job is an item on a JobList, linking an Asset to the JobList.
     * @test
     */
    public function basic() {
        $type = new \App\Model\JobType();
        $type->save();
        $jobList = new JobList();
        $jobList->type_id = $type->id;
        $jobList->save();
        // One incomplete job
        $job = new Job();
        $job->job_list_id = $jobList->id;
        $job->save();
        $this->assertEquals(1, $jobList->jobs->count());
    }

}
