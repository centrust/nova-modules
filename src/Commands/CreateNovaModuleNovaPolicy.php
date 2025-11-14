<?php

namespace Centrust\NovaModules\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CreateNovaModuleNovaPolicy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:nova-policy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates Nova Policy';

    public function handle()
    {
        $this->name = $this->ask('Module name?');
        $this->resource = $this->ask('Policy name?');
        $this->model = $this->ask('Resource class?');


        $this->generatePackageDirectories();

        $this->info('Policy Generated Successfully!');
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

        if (!is_dir($Path . '/Policies')) {
            File::makeDirectory($Path . '/Policies');
        }
        $ResourcePath = $Path . '/Policies';


        if (File::exists($ResourcePath . '/' . $this->resource)) {
            $this->error('Resource already exists');
            return;
        }

        $NameSpace = 'App\\Nova\\Modules\\' . $this->name . '\\Policies';

        $stub = file_get_contents(__DIR__ .'/../../resources/stubs/nova-policy.stub');
        $stub = str_replace('{{ user }}', 'User', $stub);
        $stub = str_replace('{{ namespace }}', $NameSpace, $stub);
        $stub = str_replace('{{ class }}', $this->resource, $stub);
//        $stub = str_replace('{{ model }}', $this->model, $stub);
//        $stub = str_replace('{{ modelVariable }}', Str::snake($this->model), $stub);
        $stub = str_replace('{{ namespacedModel }}', 'App\\Nova\\Modules\\' . $this->name . '\\Resources\\'.$this->model. ' as Resource', $stub);
        $stub = str_replace('{{ namespacedUserModel }}', 'App\Models\User', $stub);



        $ResourceToCreate = $ResourcePath . '/' . $this->resource . '.php';
        file_put_contents($ResourceToCreate, $stub);

    }
}
