<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CreateServiceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {name}';

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
        $serviceName = $this->getServiceName($name);
        $directoryPath = app_path('Services');
        $filePath = $directoryPath . DIRECTORY_SEPARATOR . $serviceName . '.php';

        // Check if the directory exists, if not, create it
        if (!File::exists($directoryPath)) {
            File::makeDirectory($directoryPath, 0755, true);
        }

        if (File::exists($filePath)) {
            $this->error("Service {$serviceName} already exists.");
            return;
        }

        $namespace = $this->getDefaultNamespace();
        $content = "<?php\n\nnamespace {$namespace};\n\nclass {$serviceName}\n{\n    // Implement your service logic here\n}\n";

        File::put($filePath, $content);

        $this->info("Service {$filePath} created successfully.");
    }

    protected function getServiceName($name)
    {
        return ucfirst($name);
    }

    protected function getDefaultNamespace()
    {
        return 'App\Services';
    }
}
