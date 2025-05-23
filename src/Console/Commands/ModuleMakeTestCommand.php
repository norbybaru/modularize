<?php

namespace NorbyBaru\Modularize\Console\Commands;

use DOMDocument;
use Illuminate\Support\Str;

class ModuleMakeTestCommand extends ModuleMakerCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make:test
                            {name : The name of the test}
                            {--module= : Name of module migration should belong to}
                            {--u|unit : Create a unit test}
                            {--p|pest : Create a Pest test}
                            {--view : Create a view test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate test resource for a module';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Test';

    public function handle(): bool|null
    {
        $module = $this->getModuleInput();
        $filename = Str::studly($this->getNameInput());
        $folder = $this->getFolderPath();

        $testType = 'Feature';
        $prefix = 'test';
        $type = '';

        if ($this->option('unit')) {
            $type = 'unit.';
            $testType = 'Unit';
        }

        if ($this->option('view')) {
            $filename = collect(explode('/', $filename))
                ->map(fn ($name) => ucwords($name))
                ->join('/');

            $filename = "View/{$filename}Test";
            $type = 'view.';
            $testType = 'Feature';

            if ($this->option('pest')) {
                $prefix = 'pest';
            }
        }

        $name = $this->qualifyClass($module.'\\'.$folder.'\\'.$testType.'\\'.$filename);

        if ($this->files->exists($path = $this->getPath($name))) {
            $this->logFileExist($name);

            return true;
        }

        if ($this->option('pest')) {
            $prefix = 'pest';
        }

        $this->setStubFile("{$prefix}.{$type}");
        $this->makeDirectory($path);

        $this->files->put($path, $this->buildClass($name));

        $this->logFileCreated($name);

        $this->updatePhpUnitXmlFile();

        return true;
    }

    protected function getFolderPath(): string
    {
        return 'Tests';
    }

    /**
     * Update phpunit.xml file with Module test directories
     */
    protected function updatePhpUnitXmlFile(): void
    {
        $path = base_path('phpunit.xml.dist');

        if (! $this->files->exists($path)) {
            $path = base_path('phpunit.xml');

            if (! $this->files->exists($path)) {
                return;
            }
        }

        /** @var \SimpleXMLElement */
        $xml = simplexml_load_file($path);
        $dom = $this->createDomDocument($xml);
        $rootDirectory = $this->getModuleRootDirectory();
        $updateDocument = false;

        // Specify the testsuite name and attribute to check
        $testSuiteName = 'Module Feature';
        $testSuiteDirectory = "./{$rootDirectory}/**/Tests/Feature";
        $testSuite = $xml->xpath("//testsuite[@name='{$testSuiteName}']");

        // Check if the attribute already exists for the child element
        if (! $testSuite || (string) $testSuite[0]->directory != $testSuiteDirectory) {
            $dom = $this->updateDocument($dom, $testSuiteName, $testSuiteDirectory, $path);
            $updateDocument = true;
        }

        $testSuiteName = 'Module Unit';
        $testSuiteDirectory = "./{$rootDirectory}/**/Tests/Unit";
        $testSuite = $xml->xpath("//testsuite[@name='{$testSuiteName}']");

        if (! $testSuite || (string) $testSuite[0]->directory != $testSuiteDirectory) {
            $dom = $this->updateDocument($dom, $testSuiteName, $testSuiteDirectory, $path);
            $updateDocument = true;
        }

        if ($updateDocument) {
            $this->saveDomDocument($dom, $path);
            $this->components->info(sprintf('[%s] updated successfully.', $path));
        }
    }

    private function createDomDocument(\SimpleXMLElement $xml): DOMDocument
    {
        // Create a new DOMDocument
        $dom = new DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;

        // Import the SimpleXML object into the DOMDocument
        $dom->loadXML($xml->asXML());

        return $dom;
    }

    /**
     * Save the modified DOMDocument back to the phpunit.xml file
     */
    private function saveDomDocument(DOMDocument $dom, string $path): void
    {
        $dom->save($path);
    }

    private function updateDocument(DOMDocument $dom, string $testSuiteName, string $testSuiteDirectory, string $path): DOMDocument
    {
        // Get the root element (<testsuites>)
        $testSuites = $dom->getElementsByTagName('testsuites')->item(0);
        // Create a new <testsuite> element
        $newTestSuite = $dom->createElement('testsuite');
        $newTestSuite->setAttribute('name', $testSuiteName);

        // Create a new <directory> element inside <testsuite>
        $newDirectory = $dom->createElement('directory', $testSuiteDirectory);
        $domAttribute = $dom->createAttribute('suffix');
        $domAttribute->value = 'Test.php';

        $newDirectory->appendChild($domAttribute);
        $newTestSuite->appendChild($newDirectory);

        // Append the new <testsuite> to <testsuites>
        $testSuites->appendChild($newTestSuite);

        return $dom;
    }
}
