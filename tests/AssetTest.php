<?php

namespace AssetManager\Tests;

use App\Model\Asset;
use App\Model\Contact;
use App\Model\Tag;

class AssetTest extends TestCase
{

    /**
     * @testdox Assets have a unique alphanumeric identifier of up to 150 characters.
     * @test
     */
    public function asset()
    {
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
    public function contacts()
    {
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
    public function tags()
    {
        $asset = new Asset();
        $asset->identifier = 'TEST123';
        $asset->save();
        $this->assertEquals(0, Tag::count());
        $this->assertEquals(0, $asset->tags->count());
        $asset->addTags('One,Two');
        $asset->load('tags');
        $this->assertEquals(2, Tag::count());
        $this->assertEquals(2, $asset->tags->count());
        $asset->addTags('One,Two,Three');
        $asset->load('tags');
        $this->assertEquals(3, $asset->tags->count());
        // Make sure empty tags aren't added.
        $asset->addTags('One,Two,,Three');
        $asset->load('tags');
        $this->assertEquals(3, $asset->tags->count());
    }

    /**
     * When adding a comment to an Asset, if the comment is already part of the Asset's comment then the new
     * comment will not be added.
     * @test
     */
    public function comments()
    {
        $asset = new Asset();
        $asset->identifier = 'TEST123';

        // Set initial comment.
        $asset->comments = 'Test comment.';
        $asset->save();
        $this->assertEquals('Test comment.', $asset->comments);

        // Appending the same string shouldn't modify it.
        $asset->appendComments('Test comment.');
        $asset->save();
        $this->assertEquals('Test comment.', $asset->comments);

        // Append a different string and it'll make sure there's a newline between them.
        $asset->appendComments('Blah.');
        $asset->save();
        $this->assertEquals("Test comment.\n\nBlah.", $asset->comments);
    }
}
