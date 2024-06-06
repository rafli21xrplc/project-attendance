<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CreateInterfaceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:interface {name}';

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
        $name = $this->argument('name');
        $className = $this->getClassName($name);
        $directoryPath = app_path('Contracts/Interfaces');
        $filePath = $directoryPath . DIRECTORY_SEPARATOR . $className . '.php';

        // Check if the directory exists, if not, create it
        if (!File::exists($directoryPath)) {
            File::makeDirectory($directoryPath, 0755, true);
        }

        if (File::exists($filePath)) {
            $this->error("Interface {$className} already exists.");
            return;
        }

        $namespace = $this->getDefaultNamespace();
        $content = "<?php\n\nnamespace {$namespace};\n\ninterface {$className}\n{\n    // Define your interface methods here\n}\n";

        File::put($filePath, $content);

        $this->info("Interface {$filePath} created successfully.");
    }

    protected function getClassName($name)
    {
        return ucfirst($name);
    }

    protected function getDefaultNamespace()
    {
        return 'App\Contracts\Interfaces';
    }
}
