<?php

use App\Model\JobResolution;

class JobResolutionTest extends TestCase
{

    /**
     * @testdox A Job Resolution has an ID, name, and a type (which defaults to 'succeeded').
     * @test
     */
    public function basic()
    {
        $jobResolution = new JobResolution();
        $jobResolution->save();
        // Three JRs are created upon installation.
        $this->assertEquals(4, $jobResolution->id);
        $this->assertEquals('', $jobResolution->name);
        $this->assertEquals(JobResolution::SUCCEEDED, $jobResolution->type, 'Defaults to suceeded');
    }
}
