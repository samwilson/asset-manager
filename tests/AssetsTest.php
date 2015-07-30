<?php

use App\Model\Asset;

class AssetsTest extends TestCase {

    /**
     * @testdox Assets have a unique alphanumeric identifier of less than 150 characters.
     * @test
     */
    public function asset() {
        $asset = new Asset();
        $asset->identifier = 'TEST123';
        $asset->save();
        $this->assertEquals(1, Asset::count());
        $this->assertEquals('TEST123', $asset->identifier);
    }

}
