<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

#[Signature('make:service {name}')]
#[Description('Make Service File Class')]
class MakeServiceCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = Str::replace('\\', '/', $this->argument('name'));

        $className = class_basename($name);

        $namespace = 'App\Services';

        $subNamespace = '';

        if (str_contains($name, '/')) {
            $subNamespace = '\\'.str_replace('/', '\\', dirname($name));
            $namespace .= $subNamespace;
        }

        $directory = app_path('Services'.($subNamespace
            ? '/'.str_replace('\\', '/', ltrim($subNamespace, '\\'))
            : ''));

        File::ensureDirectoryExists($directory);

        $path = $directory.'/'.$className.'.php';

        if (File::exists($path)) {
            $this->error('Service already exists.');

            return self::FAILURE;
        }

        $stub = File::get(base_path('stubs/service.stub'));

        $stub = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$namespace, $className],
            $stub
        );

        File::put($path, $stub);

        $this->info('Service created successfully.');

        $this->line($path);

        return self::SUCCESS;
    }
}
