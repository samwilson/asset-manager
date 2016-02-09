<?php

namespace App\Console\Commands;

use App\Model\File;

class Files extends \Illuminate\Console\Command
{

    protected $name = "files";
    protected $description = "Process files for indexing, thumbnail generation, etc.";

    /**
     * Execute the 'upgrade' console command.
     * @return void
     */
    public function fire()
    {
        foreach (File::all() as $file) {
            $this->process($file);
        }
    }

    public function process(File $file)
    {
        if ($file->isImage()) {
            $file->buildOtherSizes();
        }
    }
}
