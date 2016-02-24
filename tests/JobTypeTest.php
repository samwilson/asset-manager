<?php

namespace AssetManager\Tests;

class JobTypeTest extends TestCase
{

    /**
     * @testdox Job Types are identified by a name and two colours (background and foreground; these are optional).
     * @test
     */
    public function basic()
    {
        $jobType = new \App\Model\JobType();
        $jobType->name = 'test';
        $jobType->colour = 'red';
        $jobType->background_colour = 'green';
        $jobType->save();
        $this->assertEquals('test', $jobType->name);
        $this->assertEquals('red', $jobType->colour);
        $this->assertEquals('green', $jobType->background_colour);
    }

    /**
     * @testdox Each JobType has a workflow of Questions that is initially empty.
     * @test
     */
    public function workflow()
    {
        $jobType = new \App\Model\JobType();
        $jobType->name = 'test';
        $jobType->save();
        //$this->assertEquals(0, $jobType->questions->count());
    }

    /**
     * @testdox Some Job Types require Contacts to be contacted prior to work being carried out.
     * @test
     */
    public function contacts()
    {
//        $jobType = new \App\Model\JobType();
//        $jobType->name = 'test';
//        $jobType->save();
//        $this->assertFalse($jobType->contact_required);
//        $jobType->contact_required = true;
//        $jobType->save();
//        $this->assertTrue($jobType->contact_required);
        // Create 2 assets, add them to a Job List of this type, add a contact for them, and
    }
}
