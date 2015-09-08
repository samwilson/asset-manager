<?php

use App\Model\Crew;
use App\Model\CrewDate;

class CrewTest extends TestCase {

    /**
     * @testdox A crew is initially available to work at any time.
     * @test
     */
    public function availableAnytime() {
        $crew = new Crew(['name' => 'Test Crew']);
        $crew->save();
        $this->assertEmpty($crew->crewDates);
        $this->assertTrue($crew->availableOn('2015-09-08'));
    }

    /**
     * @testdox Crews' dates of availability are those date periods during which they can work.
     */
    public function dates() {
        $crew = new Crew(['name' => 'Test Crew']);
        $crew->save();
        $date = new CrewDate();
        $date->crew_id = $crew->id;
        $date->start_date = '2015-09-01';
        $date->save();
        $this->assertEquals(1, $crew->crewDates->count());
        $this->assertTrue($crew->availableOn('2015-09-08'));
        $this->assertFalse($crew->availableOn('2015-08-01'));
    }

}
