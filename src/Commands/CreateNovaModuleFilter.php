<?php

namespace Centrust\NovaModules\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CreateNovaModuleFilter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:filter {name?} {resource?}
                            {--boolean : Create a boolean filter}
                            {--date : Create a date filter}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates Nova Filter';

    protected string $moduleName;

    protected string $filterName;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('boolean') && $this->option('date')) {
            $this->error('The --boolean and --date options cannot be used together.');

            return self::FAILURE;
        }

        $this->moduleName = $this->argument('name') ?: $this->ask('Module name?');
        $this->filterName = $this->argument('resource') ?: $this->ask('Filter name?');

        if (! $this->generatePackageDirectories()) {
            return self::FAILURE;
        }

        $this->info('Filter Generated Successfully!');

        return self::SUCCESS;
    }

    protected function generatePackageDirectories(): bool
    {
        $Path = app_path('Nova/Modules/'.$this->moduleName.'/Filters');
        $ResourceToCreate = $Path.'/'.$this->filterName.'.php';

        if (File::exists($ResourceToCreate)) {
            $this->error('Filter already exists');

            return false;
        }

        File::ensureDirectoryExists($Path);

        $NameSpace = 'App\\Nova\\Modules\\'.$this->moduleName.'\\Filters';

        $stubName = match (true) {
            $this->option('boolean') => 'boolean-filter.stub',
            $this->option('date') => 'date-filter.stub',
            default => 'filter.stub',
        };

        $stub = file_get_contents(__DIR__.'/../../resources/stubs/'.$stubName);
        $stub = str_replace('{{ namespace }}', $NameSpace, $stub);
        $stub = str_replace('{{ class }}', $this->filterName, $stub);
        file_put_contents($ResourceToCreate, $stub);

        return true;
    }
}