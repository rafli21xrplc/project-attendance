<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CreateRepositoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository {name}';

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
        $repositoryName = $this->getRepositoryName($name);
        $directoryPath = app_path('Contracts/Repositories');
        $filePath = $directoryPath . DIRECTORY_SEPARATOR . $repositoryName . '.php';

        // Check if the directory exists, if not, create it
        if (!File::exists($directoryPath)) {
            File::makeDirectory($directoryPath, 0755, true);
        }

        if (File::exists($filePath)) {
            $this->error("Repository {$repositoryName} already exists.");
            return;
        }

        $namespace = $this->getDefaultNamespace();
        $content = "<?php\n\nnamespace {$namespace};\n\nclass {$repositoryName}\n{\n    // Define your repository methods here\n}\n";

        File::put($filePath, $content);

        $this->info("Repository {$filePath} created successfully.");
    }

    protected function getRepositoryName($name)
    {
        return ucfirst($name);
    }

    protected function getDefaultNamespace()
    {
        return 'App\Contracts\Repositories';
    }
}
