<?php

namespace NorbyBaru\Modularize\Tests\Commands;

use NorbyBaru\Modularize\Tests\MakeCommandTestCase;

class MakeTestCommandTest extends MakeCommandTestCase
{
    public function test_it_creates_a_feature_test()
    {
        $this->artisan(
            command: 'module:make:test',
            parameters: [
                'name' => 'ExampleTest',
                '--module' => $this->moduleName,
            ]
        )->assertSuccessful();

        $this->assertFileExists(filename: $this->getModulePath().'/Tests/Feature/ExampleTest.php');
    }

    public function test_it_creates_a_unit_test()
    {
        $this->artisan(
            command: 'module:make:test',
            parameters: [
                'name' => 'ExampleTest',
                '--module' => $this->moduleName,
                '--unit' => true,
            ]
        )->assertSuccessful();

        $this->assertFileExists(filename: $this->getModulePath().'/Tests/Unit/ExampleTest.php');
    }

    public function test_it_creates_a_pest_feature_test()
    {
        $this->artisan(
            command: 'module:make:test',
            parameters: [
                'name' => 'PestExampleTest',
                '--module' => $this->moduleName,
                '--pest' => true,
            ]
        )->assertSuccessful();

        $this->assertFileExists(filename: $this->getModulePath().'/Tests/Feature/PestExampleTest.php');
    }

    public function test_it_creates_a_view_test()
    {
        $this->artisan(
            command: 'module:make:test',
            parameters: [
                'name' => 'home',
                '--module' => $this->moduleName,
                '--view' => true,
            ]
        )->assertSuccessful();

        $this->assertFileExists(filename: $this->getModulePath().'/Tests/Feature/View/HomeTest.php');
    }
}
