<?php

class JobTypeTest extends TestCase {

    /**
     * @testdox Job Types are identified by a name.
     * @test
     */
    public function basic() {
        $jobType = new \App\Model\JobType();
        $jobType->name = 'test';
        $jobType->save();
        $this->assertEquals('test', $jobType->name);
    }

}
