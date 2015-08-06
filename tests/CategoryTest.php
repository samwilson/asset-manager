<?php

use App\Model\Category;

class CategoryTest extends TestCase {

    /**
     * @testdox An Asset Category has a unique name, and optionally a parent (and therefore also children).
     * @test
     */
    public function asset() {
        $parentCat = new Category();
        $parentCat->name = 'Parent';
        $parentCat->save();
        $this->assertEquals(1, Category::count());
        $this->assertEquals('Parent', $parentCat->name);
        $childCat = new Category();
        $childCat->name = 'Child';
        $childCat->save();
        $parentCat->childCategories()->save($childCat);
        $this->assertEquals(2, Category::count());
        $this->assertEquals(1, $parentCat->childCategories->count());
        $this->assertEquals('Parent', $childCat->parentCategory->name);
    }

    /**
     * @testdox Categories can be nested to any depth.
     * @test
     */
    public function nesting() {
        $parent = new Category();
        $parent->name = 'Parent';
        $parent->save();
        $child1 = new Category();
        $child1->name = 'Child 1';
        $child1->save();
        $child2 = new Category();
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
