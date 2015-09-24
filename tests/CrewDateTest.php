<?php

use App\Model\Crew;
use App\Model\CrewDate;

class CrewDateTest extends TestCase {

    /**
     * @testdox
     * @test
     */
    public function dates() {
        $crew = new Crew(['name' => 'Test Crew']);
        $crew->save();

        // Open start date.
        $date1 = new CrewDate();
        $date1->crew_id = $crew->id;
        $date1->end_date = '2015-09-01';
        $date1->save();
        $this->assertNull($date1->start_date);
        $this->assertFalse($date1->availableOn('2015-08-01'));
        $this->assertTrue($date1->availableOn('2015-09-08'));

        // Both dates given.
        $date2 = new CrewDate();
        $date2->crew_id = $crew->id;
        $date2->start_date = '2015-09-10';
        $date2->end_date = '2015-09-20';
        $date2->save();
        $this->assertTrue($date2->availableOn('2015-09-09'));
        $this->assertFalse($date2->availableOn('2015-09-10'));
        $this->assertFalse($date2->availableOn('2015-09-12'));
        $this->assertFalse($date2->availableOn('2015-09-20'));
        $this->assertTrue($date2->availableOn('2015-09-21'));

        // Open end date.
        $date3 = new CrewDate();
        $date3->crew_id = $crew->id;
        $date3->start_date = '2015-10-05';
        $date3->save();
        $this->assertTrue($date3->availableOn('2015-10-04'));
        $this->assertFalse($date3->availableOn('2015-10-06'));
    }

}
