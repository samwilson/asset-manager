<?php

use App\Model\AssetCategory;

class AssetCategoryTest extends TestCase {

    /**
     * @testdox An Asset Category has a unique name, and optionally a parent (and therefore also children).
     * @test
     */
    public function asset() {
        $parentCat = new AssetCategory();
        $parentCat->name = 'Parent';
        $parentCat->save();
        $this->assertEquals(1, AssetCategory::count());
        $this->assertEquals('Parent', $parentCat->name);
        $childCat = new AssetCategory();
        $childCat->name = 'Child';
        $childCat->save();
        $parentCat->childCategories()->save($childCat);
        $this->assertEquals(2, AssetCategory::count());
        $this->assertEquals(1, $parentCat->childCategories->count());
        $this->assertEquals('Parent', $childCat->parentCategory->name);
    }

    /**
     * @testdox
     * @test
     */
    public function nesting() {
        $parent = new AssetCategory();
        $parent->name = 'Parent';
        $parent->save();
        $child1 = new AssetCategory();
        $child1->name = 'Child 1';
        $child1->save();
        $child2 = new AssetCategory();
        $child2->name = 'Child 2';
        $child2->save();
        $parent->childCategories()->save($child1);
        $child1->childCategories()->save($child2);
        $this->assertEquals('Parent', $child2->parentCategory->parentCategory->name);
        $this->assertCount(1, $parent->getChildren());
        $this->assertCount(1, $child1->getChildren());
        $this->assertCount(0, $child2->getChildren());
    }

}
