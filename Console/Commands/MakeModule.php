<?php

namespace IndyKoning\ModuleCreate\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class MakeModule extends Command
{
    protected $signature = 'make:module {vendor} {package} {--json-vendor=} {--json-package=} {--stub=}';

    protected $description = 'Create a module!';

    protected $vendor;
    protected $jsonVendor;
    protected $package;
    protected $JsonPackage;
    protected $relPath;
    protected $packagePath;

    public function handle()
    {
        $vendor = $this->vendor = $this->argument('vendor');
        $package = $this->package = $this->argument('package');

        $jsonVendor = $this->jsonVendor = $this->option('json-vendor') ?? Str::lower($this->vendor);
        $jsonPackage = $this->jsonPackage = $this->option('json-package') ?? Str::kebab($this->package);

        $relPath = $this->relPath = __('Modules/:vendor/:package', @compact('vendor', 'package'));
        $packagePath = $this->packagePath = base_path($this->relPath);

        $this->info(__('Creating directory at: :path', ['path' => $relPath]));
        File::makeDirectory($this->packagePath, 0755, true, true);
        $this->newLine();

        $this->info(__('Placing stubs in the folder, and building module'));
        $this->newLine();

        $stubFunction = 'run' . Str::of($this->option('stub') ?? '')->title()->replace(' ', '') . 'Stub';
        method_exists($this, $stubFunction) ? $this->{$stubFunction}() : $this->runDefaultStub();
        $this->newLine();

        $this->info(__('Configuring composer repository path'));
        Process::fromShellCommandline(__('composer config repositories.:jsonVendor-:jsonPackage path "./:relPath" --file composer.json', @compact('relPath', 'jsonVendor', 'jsonPackage')))
            ->setTty(true)
            ->setTimeout(null)
            ->run();
        $this->newLine();

        $this->info(__('Installing your new module 🎉'));
        Process::fromShellCommandline(__('composer require ":jsonVendor/:jsonPackage:@dev"', @compact('jsonVendor', 'jsonPackage')))
            ->setTty(true)
            ->setTimeout(null)
            ->run();
        $this->newLine();

        $this->info(__('We\'re all done here! 🥳'));
    }

    /**
     * Create package using very basic stubs.
     */
    public function runDefaultStub()
    {
        extract(get_object_vars($this));
        File::put($this->packagePath . '/composer.json', __(File::get(__DIR__. '/../../stubs/composer.json'), @compact('vendor', 'package', 'jsonVendor', 'jsonPackage')));
        File::put($this->packagePath . '/' . $this->package . 'ServiceProvider.php', __(File::get(__DIR__. '/../../stubs/ServiceProvider.php'), @compact('vendor', 'package', 'jsonVendor', 'jsonPackage')));
    }

    /**
     * Download and use the spatie package skeleton.
     */
    public function runSpatieStub()
    {
        $this->info(__('Cloning the spatie skeleton package into: :path', ['path' => $this->relPath]));
        Process::fromShellCommandline(__('cd :packagePath && git clone https://github.com/spatie/package-skeleton-laravel.git .', ['packagePath' => $this->packagePath]))
            ->setTty(true)
            ->setTimeout(null)
            ->run();
        $this->newLine();

        $this->info(__('running configurator package...'));
        $this->warn(__('We\'re kind of expecting you to enter the following:'));
        $this->warn(__('Vendor name: :jsonVendor', ['jsonVendor' => $this->jsonVendor]));
        $this->warn(__('Vendor namespace: :vendor', ['vendor' => $this->vendor]));
        $this->warn(__('Package name :jsonPackage', ['jsonPackage' => $this->jsonPackage]));
        $this->warn(__('Class name :package', ['package' => $this->package]));

        Process::fromShellCommandline(__('cd :packagePath && php ./configure.php', ['packagePath' => $this->packagePath]))
            ->setTty(true)
            ->setTimeout(null)
            ->run();
    }
}
