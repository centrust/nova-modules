---
name: nova-modules-development
description: Build modular Laravel Nova features with centrust/nova-modules. Use when creating or modifying Nova modules, resources, actions, filters, model policies, or Nova resource policies under app/Nova/Modules.
---

# Nova Modules Development

## When to use

Activate this skill when working with `centrust/nova-modules`, its `module:*` Artisan generators, or Nova components organized into modules.

## Module conventions

Modules live at `app/Nova/Modules/{Module}` with namespaces rooted at `App\Nova\Modules\{Module}`. Use simple StudlyCase module and class names without `.php` extensions. Follow existing application naming conventions.

```text
app/Nova/Modules/Sales/
├── Actions/
├── Enums/
├── Filters/
├── Lenses/
├── Metrics/
├── Policies/
├── Resources/
├── Rules/
└── Services/
```

The package scaffolds directories and classes; it does not implement domain logic or automatically register resources, filters, or policies. Its published `nova-modules` configuration is currently empty; do not invent configuration keys or facade methods.

## Generate components

Run these commands from the consuming Laravel application, supplying every positional argument to avoid interactive prompts:

```shell
php artisan module:create Sales
php artisan module:resource Sales Order Order
php artisan module:action Sales ApproveOrder
php artisan module:filter Sales OrderStatus
php artisan module:filter Sales OrderFlags --boolean
php artisan module:filter Sales CreatedAfter --date
php artisan module:policy Sales OrderPolicy Order
php artisan module:nova-policy Sales OrderNovaPolicy Order
```

| Command | Positional arguments | Output |
| --- | --- | --- |
| `module:create` | Module name | All module directories |
| `module:resource` | Module, resource class, model name | `Resources/{Resource}.php` |
| `module:action` | Module, action class | `Actions/{Action}.php` |
| `module:filter` | Module, filter class | `Filters/{Filter}.php` |
| `module:policy` | Module, policy class, model name | `Policies/{Policy}.php` |
| `module:nova-policy` | Module, policy class, resource class | `Policies/{Policy}.php` |

Generators create their required module subdirectories. There are no package generators for metrics, lenses, rules, services, or enums; follow existing application patterns when adding these.

Inspect the destination before generating: `module:filter` refuses to overwrite an existing filter's `.php` file, but other generators' existing-file checks do not reliably protect `.php` files from being overwritten. Edit existing classes instead of regenerating them.

## Resources and actions

`module:resource Sales Order Order` creates `App\Nova\Modules\Sales\Resources\Order`, extending the application's `App\Nova\Resource`, and sets `$model` to `App\Models\Order::class`. The model must already exist or be created separately. Supply a model name relative to `App\Models`, not a fully qualified class name; adjust the generated reference for other model namespaces.

Complete the generated resource's fields, title, search columns, and relationships. Attach module actions through its `actions(NovaRequest $request)` method:

```php
use App\Nova\Modules\Sales\Actions\ApproveOrder;

public function actions(NovaRequest $request): array
{
    return [new ApproveOrder];
}
```

Implement action behavior in `handle(ActionFields $fields, Collection $models)` and action inputs in `fields(NovaRequest $request)`. Queue traits are included, but the stub does not implement `ShouldQueue`; explicitly configure queueing when required.

Inspect the application's Nova service provider and existing discovery conventions. If the module resource is not discovered, explicitly register it in the application's resource-registration flow:

```php
\Laravel\Nova\Nova::resources([
    \App\Nova\Modules\Sales\Resources\Order::class,
]);
```

## Filters

Use `module:filter {name?} {resource?}` with the module name and filter class name. Missing arguments prompt for the module and filter names. Without options, it generates a select filter extending `Laravel\Nova\Filters\Filter`. Use `--boolean` for `BooleanFilter` or `--date` for `DateFilter`; each variant has its own stub. The options cannot be used together.

Implement query constraints in `apply(NovaRequest $request, $query, $value)`; the generated stubs return the query without filtering it. Define selectable labels and values in `options(NovaRequest $request)` for select and boolean filters. Boolean filters receive an array of selected option values; date filters parse the supplied value with `Carbon::parse($value)` and do not define an `options` method.

Attach filters through the resource's `filters(NovaRequest $request)` method:

```php
use App\Nova\Modules\Sales\Filters\OrderStatus;

public function filters(NovaRequest $request): array
{
    return [new OrderStatus];
}
```

## Choose and wire policies

### Eloquent model policy

Use `module:policy Sales OrderPolicy Order` for authorization against `App\Models\Order`. The generated methods declare `bool` but have empty bodies: implement every applicable method before use, with explicit authorization decisions. The stub assumes the user model is `App\Models\User`; adjust it if needed.

Do not assume Laravel discovers policies inside module directories. Register the model-policy mapping in the application's authorization bootstrapping when no existing custom discovery handles it:

```php
\Illuminate\Support\Facades\Gate::policy(
    \App\Models\Order::class,
    \App\Nova\Modules\Sales\Policies\OrderPolicy::class,
);
```

### Nova resource policy

Use `module:nova-policy Sales OrderNovaPolicy Order` for a Nova 5 resource policy. The final argument names a resource in the same module's `Resources` namespace, not an Eloquent model. The generated policy imports it as `Resource` and accepts resource instances for record-level methods.

All generated methods return `false`. Replace these defaults only with the application's intended authorization rules, and attach the policy to the Nova resource:

```php
public static $policy = \App\Nova\Modules\Sales\Policies\OrderNovaPolicy::class;
```

Keep model policy and resource policy signatures distinct; do not replace a resource argument with an Eloquent model argument or blindly enable every ability.

## Verification checklist

- Confirm generated file paths, namespaces, model imports, and the application's base Nova resource exist.
- Confirm module resources are registered and actions and filters appear on the intended resources.
- Test filter query behavior for select values, selected and unselected boolean options, and date boundaries as applicable.
- Test both allowed and denied authorization paths, including the correct argument type for each policy.
- Follow the application's existing test conventions and run targeted tests for the behavior changed.