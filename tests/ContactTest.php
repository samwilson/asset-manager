<?php

class ContactTest extends TestCase {

    /**
     * @testdox A Contact has a name and two optional phone numbers. They are allocated an ID number.
     * @test
     */
    public function basic() {
        $contact = new \App\Model\Contact();
        $contact->name = 'Test Person';
        $contact->phone_1 = '12345';
        $contact->save();
        $this->assertEquals('Test Person', $contact->name);
        $this->assertEquals('12345', $contact->phone_1);
        $contact->phone_2 = '4567';
        $contact->save();
        $this->assertEquals('4567', $contact->phone_2);
    }

    /**
     * @testdox Contacts can be related to any number of assets.
     * @test
     */
    public function assets() {
        
    }

    /**
     * @testdox Every time a Contact is contacted, details of the interaction can be recorded.
     * @test
     */
    public function contacts() {
        
    }

    /**
     * @testdox A Job Type can require a Contact to be contacted, so a Contact can have a list of outstanding required contact-attempts.
     * @test
     */
    public function requirement() {
        
    }

}
