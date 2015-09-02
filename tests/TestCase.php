<?php

use Illuminate\Support\Facades\DB;

class TestCase extends Illuminate\Foundation\Testing\TestCase {

    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    public function setUp() {
        parent::setUp();
        \Artisan::call('upgrade');
        //DB::beginTransaction();
    }

    public function tearDown() {
        
        $col = 'Tables_in_' . getenv('DB_DATABASE');
        DB::statement("SET FOREIGN_KEY_CHECKS=0");
        foreach (DB::select("SHOW TABLES") as $table) {
            $tableName = $table->$col;
            DB::statement("DROP TABLE IF EXISTS `$tableName`");
            //DB::statement("TRUNCATE `$tableName`");
        }
        DB::statement("SET FOREIGN_KEY_CHECKS=1");

        //DB::rollBack();
        parent::tearDown();
    }

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication() {
        $app = require __DIR__ . '/../bootstrap/app.php';
        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        return $app;
    }

}
