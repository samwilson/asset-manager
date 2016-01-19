<?php

use App\Model\User;
use App\Model\UserUnavailability;
use App\Model\QueuedEmail;

class UserTest extends TestCase {

    /**
     * @testdox Dates of non-availability can be specified for users (in exactly the same way as for Crews).
     * @test
     */
    public function dates() {
        $user = new User(['username' => 'test']);
        $user->save();

        $date = new UserUnavailability();
        $date->user_id = $user->id;
        $date->start_date = '2000-09-10';
        $date->end_date = '2000-09-15';
        $date->save();

        // First test the date.
        $this->assertTrue($date->availableOn('2000-09-09'));
        $this->assertFalse($date->availableOn('2000-09-10'));
        $this->assertFalse($date->availableOn('2000-09-15'));
        $this->assertTrue($date->availableOn('2000-09-16'));

        // Then test the user.
        $this->assertTrue($user->availableOn('2000-09-09'));
        $this->assertFalse($user->availableOn('2000-09-10'));
        $this->assertFalse($user->availableOn('2000-09-15'));
        $this->assertTrue($user->availableOn('2000-09-16'));
    }

    /**
     * @testdox Requesting a password reminder adds an item to the Mail Queue.
     * @test
     */
    public function email_reminder() {
        $user = new User(['username' => 'test']);
        $user->save();
        $user->sendPasswordReminder();
        $this->assertEquals(1, QueuedEmail::count());
    }

}
