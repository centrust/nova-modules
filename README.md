# Laravel Nova Modular Architecture

[![Latest Version on Packagist](https://img.shields.io/packagist/v/centrust/nova-modules.svg?style=flat-square)](https://packagist.org/packages/centrust/nova-modules)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/centrust/nova-modules/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/centrust/nova-modules/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/centrust/nova-modules/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/centrust/nova-modules/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/centrust/nova-modules.svg?style=flat-square)](https://packagist.org/packages/centrust/nova-modules)

This package provides a modular architecture for Laravel Nova, allowing you to organize your Nova resources, actions, policies, and more into dedicated modules. It helps keep your `app/Nova` directory clean and improves code maintainability by grouping related functionality.

## Installation

You can install the package via composer:

```bash
composer require centrust/nova-modules
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="nova-modules-config"
```

## Module Architecture

The package organizes modules under the `app/Nova/Modules` directory. Each module has its own subdirectory containing its specific components:

```text
app/Nova/Modules/
└── YourModuleName/
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

## Usage

The package provides several Artisan commands to generate modules and their components.

### Create a New Module

To create the directory structure for a new module:

```bash
php artisan module:create {name?}
```
This command will ask for the module name (if not provided) and generate the standard directory structure.

### Generate Module Components

You can generate specific components within a module using the following commands:

#### Create a Resource
```bash
php artisan module:resource {module?} {resource?} {model?}
```
Prompts for (if not provided): Module name, Resource name, and Model name.

#### Create an Action
```bash
php artisan module:action {module?} {action?}
```
Prompts for (if not provided): Module name and Action name.

#### Create a Policy
```bash
php artisan module:policy {module?} {policy?} {model?}
```
Prompts for (if not provided): Module name, Policy name, and Model name. This generates a standard Laravel policy that applies to the Eloquent Model.

#### Create a Nova Policy
```bash
php artisan module:nova-policy {module?} {policy?} {resource?}
```
Prompts for (if not provided): Module name, Policy name, and Resource class.
This generates a Nova-specific policy where the resource itself
is passed instead of the Model. This is specifically useful for Nova V5
where policies can be directly applied to resources. Like: public static $policy =<nova-policy::class>; inside the nova resource

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Salah](https://github.com/centrust)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
