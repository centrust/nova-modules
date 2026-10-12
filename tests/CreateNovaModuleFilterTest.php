<?php

use Illuminate\Support\Facades\File;

beforeEach(function () {
    $this->filterTestPath = base_path('nova-filter-tests-'.uniqid());
    $this->app->useAppPath($this->filterTestPath.'/app');
});

afterEach(function () {
    File::deleteDirectory($this->filterTestPath);
});

it('generates a filter in a new module', function () {
    $this->artisan('module:filter', ['name' => 'Sales', 'resource' => 'OrderStatus'])
        ->expectsOutput('Filter Generated Successfully!')
        ->assertExitCode(0);

    $path = app_path('Nova/Modules/Sales/Filters/OrderStatus.php');

    expect(File::exists($path))->toBeTrue();
    expect(File::get($path))
        ->toContain('namespace App\\Nova\\Modules\\Sales\\Filters;')
        ->toContain('class OrderStatus extends Filter')
        ->toContain("public \$component = 'select-filter';")
        ->toContain('public function apply(NovaRequest $request, $query, $value)')
        ->toContain('return $query;')
        ->toContain('public function options(NovaRequest $request)')
        ->not->toContain('{{');
});

it('generates the requested filter variant', function (string $option, string $baseClass) {
    $this->artisan('module:filter', ['name' => 'Sales', 'resource' => 'OrderStatus', $option => true])
        ->expectsOutput('Filter Generated Successfully!')
        ->assertExitCode(0);

    $contents = File::get(app_path('Nova/Modules/Sales/Filters/OrderStatus.php'));

    expect($contents)
        ->toContain('namespace App\\Nova\\Modules\\Sales\\Filters;')
        ->toContain('use Laravel\\Nova\\Filters\\'.$baseClass.';')
        ->toContain('class OrderStatus extends '.$baseClass)
        ->toContain('public function apply(NovaRequest $request, Builder $query, mixed $value): Builder')
        ->toContain('return $query;')
        ->not->toContain('{{');

    if ($option === '--date') {
        expect($contents)
            ->toContain('use Illuminate\\Support\\Carbon;')
            ->toContain('$value = Carbon::parse($value);')
            ->not->toContain('public function options');
    } else {
        expect($contents)->toContain('public function options(NovaRequest $request): array');
    }
})->with([
    'boolean' => ['--boolean', 'BooleanFilter'],
    'date' => ['--date', 'DateFilter'],
]);

it('rejects conflicting filter options without creating a file', function () {
    $this->artisan('module:filter', ['name' => 'Sales', 'resource' => 'OrderStatus', '--boolean' => true, '--date' => true])
        ->expectsOutput('The --boolean and --date options cannot be used together.')
        ->doesntExpectOutput('Filter Generated Successfully!')
        ->assertExitCode(1);

    expect(File::exists(app_path('Nova/Modules/Sales/Filters/OrderStatus.php')))->toBeFalse();
});

it('prompts for missing module and filter names', function (array $options) {
    $this->artisan('module:filter', $options)
        ->expectsQuestion('Module name?', 'Sales')
        ->expectsQuestion('Filter name?', 'OrderStatus')
        ->expectsOutput('Filter Generated Successfully!')
        ->assertExitCode(0);

    expect(File::exists(app_path('Nova/Modules/Sales/Filters/OrderStatus.php')))->toBeTrue();
})->with([
    'select' => [[]],
    'boolean' => [['--boolean' => true]],
    'date' => [['--date' => true]],
]);

it('does not overwrite an existing filter', function (array $options) {
    $path = app_path('Nova/Modules/Sales/Filters/OrderStatus.php');
    File::ensureDirectoryExists(dirname($path));
    File::put($path, '<?php // Existing filter');

    $this->artisan('module:filter', array_merge(['name' => 'Sales', 'resource' => 'OrderStatus'], $options))
        ->expectsOutput('Filter already exists')
        ->doesntExpectOutput('Filter Generated Successfully!')
        ->assertExitCode(1);

    expect(File::get($path))->toBe('<?php // Existing filter');
})->with([
    'select' => [[]],
    'boolean' => [['--boolean' => true]],
    'date' => [['--date' => true]],
]);