# Laravel Module Create

This module adds a command to easily generate "modules" in Laravel and install them using composer

## Installation

Simply install the package using composer

`composer require indykoning/laravel-module-create --dev`

Since this module uses composer to install and autoload the created modules this module can be removed while still keeping created modules functional.

## Usage

`php artisan make:module {vendor} {package} {--json-vendor=} {--json-package=} {--stub=}`

if json-vendor and json-package are not defined, we will make assumptions based on the vendor and package name

The possible values of stub are: 
 - spatie (uses the [spatie skeleton](https://github.com/spatie/package-skeleton-laravel), may prove a somewhat unstable on old laravel installations but is much more fully featured)
 - default (the VERY basics of what you need for an install)

## Configuration

If you wish to change the folder that the module installs new modules to you can publish the config

```
php artisan vendor:publish --provider="IndyKoning\ModuleCreate\ModuleCreateServiceProvider" --tag="config"
```

and change the `module-folder`

NOTE: `module-folder` is assumed to be relative from the laravel installation, so do not attempt to use an absolute path. Subfolders are fine though.

## Internals

1. We very simply create the required folders for the vendor and package name
2. Then we add the repository path to the composer.json
3. Then we install the repository from that path
4. Laravel should now auto discover your newly created module and you can get to work
