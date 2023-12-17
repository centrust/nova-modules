<?php

namespace Centrust\NovaModules\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class CreateNovaModuleResource extends ModuleCommandAbstract
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:resource';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    protected mixed $resource;
    protected $name;
    protected $model;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->name = $this->ask('Module name?');
        $this->resource = $this->ask('Resource name?');
        $this->model = $this->ask('Resource model?');


        $this->generatePackageDirectories();

        $this->info('Resource Generated Successfully!');
    }


    /**
     * Create package directories
     * @return void
     */
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

        if (!is_dir($Path . '/Resources')) {
            File::makeDirectory($Path . '/Resources');
        }
        $ResourcePath = $Path . '/Resources';


        if (File::exists($ResourcePath . '/' . $this->resource)) {
            $this->error('Resource already exists');
            return;
        }

        $NameSpace = 'App\\Nova\\Modules\\' . $this->name . '\\Resources';

        $stub = file_get_contents('/../../resource.stub');
        $stub = str_replace('{{ namespace }}', $NameSpace, $stub);
        $stub = str_replace('{{ class }}', $this->resource, $stub);
        $stub = str_replace('{{ namespacedModel }}', 'App\\Models\\' . $this->model, $stub);
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
