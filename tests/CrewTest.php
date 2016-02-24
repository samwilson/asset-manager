<?php

namespace AssetManager\Tests;

use App\Model\Crew;
use App\Model\CrewUnavailability;

class CrewTest extends TestCase
{

    /**
     * @testdox A crew is initially available to work at any time.
     * @test
     */
    public function availableAnytime()
    {
        $crew = new Crew(['name' => 'Test Crew']);
        $crew->save();
        $this->assertEmpty($crew->crewDates);
        $this->assertTrue($crew->availableOn('2015-09-08'));
    }

    /**
     * @testdox Crews' dates of unavailability are those date periods during which they can not work.
     */
    public function dates()
    {
        $crew = new Crew(['name' => 'Test Crew']);
        $crew->save();

        // Open start date.
        $date1 = new CrewUnavailability();
        $date1->crew_id = $crew->id;
        $date1->end_date = '2015-09-01';
        $date1->save();
        $this->assertEquals(1, $crew->crewUnavailabilities->count());
        $this->assertTrue($crew->availableOn('2015-09-08'));
        $this->assertFalse($crew->availableOn('2015-08-01'));

        // Closed both ends.
        $date2 = new CrewUnavailability();
        $date2->crew_id = $crew->id;
        $date2->start_date = '2015-09-10';
        $date2->end_date = '2015-09-20';
        $date2->save();
        $crew->load('crewUnavailabilities');
        $this->assertEquals(2, $crew->crewUnavailabilities->count());
        $this->assertFalse($crew->availableOn('2015-09-10'));
        $this->assertFalse($crew->availableOn('2015-09-12'));
        $this->assertFalse($crew->availableOn('2015-09-20'));
        $this->assertTrue($crew->availableOn('2015-09-21'));

        // Open end date.
        $date3 = new CrewUnavailability();
        $date3->crew_id = $crew->id;
        $date3->start_date = '2015-10-05';
        $date3->save();
        $crew->load('crewUnavailabilities');
        $this->assertEquals(3, $crew->crewUnavailabilities->count());
        $this->assertTrue($crew->availableOn('2015-10-04'));
        $this->assertFalse($crew->availableOn('2015-10-06'));

        // All previous availabilities should still hold.
        $this->assertTrue($crew->availableOn('2015-09-08'));
        $this->assertFalse($crew->availableOn('2015-08-01'));
        $this->assertFalse($crew->availableOn('2015-09-10'));
        $this->assertFalse($crew->availableOn('2015-09-12'));
        $this->assertFalse($crew->availableOn('2015-09-20'));
        $this->assertTrue($crew->availableOn('2015-09-21'));
        $this->assertTrue($crew->availableOn('2015-10-04'));
        $this->assertFalse($crew->availableOn('2015-10-06'));
    }
}
