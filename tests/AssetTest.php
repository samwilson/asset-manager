<?php

use App\Model\Asset;
use App\Model\Custodian;

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
     * @testdox Assets can have Custodians, who may need to be contacted prior to Jobs being carried out.
     * @test
     */
    public function custodians() {
        $asset = new Asset();
        $asset->identifier = 'TEST123';
        $asset->save();
        $custodian = new Custodian();
        $custodian->name = 'Bill';
        $custodian->save();
        $asset->custodians()->attach($custodian->id);
        $this->assertEquals(1, $asset->custodians->count());
        $this->assertEquals(1, $custodian->assets->count());
    }

}
