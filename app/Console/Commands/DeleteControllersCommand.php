<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class DeleteControllersCommand extends Command
{
    protected $signature = 'app:delete-controllers';

    protected $description = 'Delete Controllers and Database directories';

    public function handle()
    {
        $paths = [
            app_path('Http/Controllers'),
            base_path('database'),
        ];

        foreach ($paths as $path) {
            if (File::exists($path)) {
                File::deleteDirectory($path, true);

                $this->info("Deleted: {$path}");
            } else {
                $this->warn("Not found: {$path}");
            }
        }

        return Command::SUCCESS;
    }
}
