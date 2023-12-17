<?php

namespace Centrust\NovaModules\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CreateNovaModuleAction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:action';

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
        $this->name = $this->ask('Module name?');
        $this->resource = $this->ask('Action name?');
     //   $this->model = $this->ask('Resource model?');


        $this->generatePackageDirectories();

        $this->info('Action Generated Successfully!');
    }

    protected function generatePackageDirectories()
    {
        $Path = app_path('Nova/Modules');

        if (!is_dir($Path)) {
            File::makeDirectory($Path);
        }

        if (!is_dir($Path . '/' . $this->name)) {
            File::makeDirectory($Path . '/' . $this->name);
        }
        $Path = $Path . '/' . $this->name;

        if (!is_dir($Path . '/Actions')) {
            File::makeDirectory($Path . '/Actions');
        }
        $ResourcePath = $Path . '/Actions';


        if (File::exists($ResourcePath . '/' . $this->resource)) {
            $this->error('Resource already exists');
            return;
        }

        $NameSpace = 'App\\Nova\\Modules\\' . $this->name . '\\Actions';

        $stub = file_get_contents($this->package->basePath('/../resources/stubs/action.stub'));
        $stub = str_replace('{{ namespace }}', $NameSpace, $stub);
        $stub = str_replace('{{ class }}', $this->resource, $stub);
        $ResourceToCreate = $ResourcePath . '/' . $this->resource . '.php';
        file_put_contents($ResourceToCreate, $stub);

        return;


//        try {
//            $Response = $this->callSilently('nova:resource', [
//                'name' => $ResourceToCraete,
//            ]);
//        } catch (\Exception $e) {
//            $this->error($e->getMessage());
//            return;
//        }

//        $this->info($Response);
    }

}
