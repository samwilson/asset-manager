<?php

class UserTest extends TestCase {

    /**
     * User dates of non-availability are inclusive.
     * @test
     */
    public function dates() {
        $user = new \App\Model\User(['username' => 'test']);
        $user->save();

        $date = new App\Model\UserDate();
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

}
