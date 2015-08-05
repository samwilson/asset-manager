<?php

use App\Model\JobPack;

class JobPackTest extends TestCase {

    /**
     * @testdox A Job Pack is a collection of Assets that must have a particular Job performed on them.
     * @test
     */
    public function basic() {
        $jobPack = new JobPack();
        $this->assertEquals(0, $jobPack->assets->count());
    }

    /**
     * @testdox It is possible to retrieve a list of the Assets in the Job Pack that have not yet had their Custodians contacted.
     * @test
     */
    public function y() {
        
    }

}
