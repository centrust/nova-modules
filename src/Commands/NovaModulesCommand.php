<?php

namespace Centrust\NovaModules\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class NovaModulesCommand extends Command
{
    protected $name;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->name = $this->ask('What is Module name?');

        $this->generatePackageDirectories();

        $this->info('Directory Structure Generated Successfully!');
    }


    /**
     * Create package directories
     * @return void
     */
    protected function generatePackageDirectories()
    {

        $Path = app_path('Nova/Modules');

        if (!is_dir($Path)) {
            File::makeDirectory($Path );
        }

        $Path = $Path . '/' . $this->name;
        if (!is_dir($Path)) {
            File::makeDirectory($Path );
        }
        $directories = [
            'Resources',
            'Actions',
            'Filters',
            'Metrics',
            'Lenses',
            'Policies',
            'Rules',
            'Services',
            'Enums',
        ];
        foreach ($directories as $directory) {
            if (!is_dir($Path . '/' . $directory)) {
                File::makeDirectory($Path . '/' . $directory);
            }

        }

    }
}
