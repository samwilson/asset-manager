<?php

use App\Model\Tag;

class JobTypeTest extends TestCase
{

    public function testBasics()
    {
        $tag = new Tag(['name' => 'Testing']);
        $tag->save();
        $this->assertSame(1, $tag->id);
        $this->assertSame('Testing', $tag->name);
    }

    public function testMerge()
    {
        $tag1 = Tag::firstOrCreate(['name' => 'Tag 1']);
        $tag2 = Tag::firstOrCreate(['name' => 'Tag 2']);
        $this->assertSame(2, Tag::count());
        Tag::merge($tag1, $tag2);
        $this->assertSame(1, Tag::count());
    }
}
