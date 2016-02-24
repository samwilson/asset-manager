<?php

namespace AssetManager\Tests;

use App\Model\Asset;
use App\Model\Tag;
use App\Model\JobList;

class TagTest extends TestCase
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
        // Basic merge.
        $tag1 = Tag::firstOrCreate(['name' => 'Tag 1']);
        $tag2 = Tag::firstOrCreate(['name' => 'Tag 2']);
        $this->assertSame(2, Tag::count());
        $tag1->merge($tag2);
        $this->assertSame(1, Tag::count());
        $this->assertSame('Tag 1', Tag::first()->name);

        // Merge when the other-tag has assets attached.
        $tag3 = Tag::firstOrCreate(['name' => 'Tag 3']);
        $asset = Asset::firstOrCreate(['identifier' => 'Asset 1']);
        $asset->addTags('Tag 3');
        $this->assertSame(2, Tag::count());
        $tag1->merge($tag3);
        $this->assertSame(1, Tag::count());
        $this->assertSame(1, $tag1->assets->count());
        $this->assertSame('Tag 1', $asset->tagsAsString());

        // What about when a JobList is already tagged with both tags?
        $type = new \App\Model\JobType();
        $type->save();
        $jobList = new JobList();
        $jobList->name = 'JL1';
        $jobList->type_id = $type->id;
        $jobList->save();
        $jobList->addTags('Tag 1, Tag 4, Tag 5');
        $tag4 = Tag::firstOrCreate(['name' => 'Tag 4']);
        $this->assertSame(3, Tag::count());
        $tag1->merge($tag4);
        $this->assertSame(2, Tag::count());
        $this->assertSame('Tag 1, Tag 5', $jobList->tagsAsString());
    }

    public function testMergeIntoSelf()
    {
        $tag = Tag::firstOrCreate(['name' => 'Tag 1']);
        $this->assertSame(1, Tag::count());
        $tag->merge($tag);
        $this->assertSame(1, Tag::count());
    }
}
