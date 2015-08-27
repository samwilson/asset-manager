<?php

use App\Model\Asset;
use App\Model\Contact;

class AssetTest extends TestCase {

    /**
     * @testdox Assets have a unique alphanumeric identifier of up to 150 characters.
     * @test
     */
    public function asset() {
        $asset = new Asset();
        $asset->identifier = 'TEST123';
        $asset->save();
        $this->assertEquals(1, Asset::count());
        $this->assertEquals('TEST123', $asset->identifier);
        $asset2 = new Asset();
        $asset2->identifier = str_repeat('A', 180);
        $asset2->save();
        $this->assertEquals(str_repeat('A', 150), $asset2->identifier);
    }

    /**
     * @testdox Assets have zero or more Contacts.
     * @test
     */
    public function contacts() {
        $asset = new Asset();
        $asset->identifier = 'TEST123';
        $asset->save();
        $contact = new Contact();
        $contact->name = 'Bill';
        $contact->save();
        $asset->contacts()->attach($contact->id);
        $this->assertEquals(1, $asset->contacts->count());
        $this->assertEquals(1, $contact->assets->count());
    }

    /**
     * @testdox Assets can be tagged.
     * @test
     */
    public function tags() {
        $asset = new Asset();
        $asset->identifier = 'TEST123';
        $asset->save();
        $this->assertEquals(0, $asset->tags->count());
        $asset->addTags('One,Two');
        $this->assertEquals(2, $asset->tags->count());
        $asset->addTags('One,Two,Three');
        $this->assertEquals(3, $asset->tags->count());
    }

}
