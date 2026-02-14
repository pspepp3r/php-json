<?php

declare(strict_types=1);

namespace PhpJson\Tests\Adapters;

use PHPUnit\Framework\TestCase;
use PhpJson\Adapters\JsonAdapter;
use PhpJson\Marshals\Json\MakeMarshal;
use PhpJson\Marshals\Json\EditMarshal;

final class JsonAdapterTest extends TestCase
{
    private string $testDir;

    protected function setUp(): void
    {
        $this->testDir = sys_get_temp_dir() . '/php-json-tests-' . uniqid();
        mkdir($this->testDir);
    }

    protected function tearDown(): void
    {
        // Clean up test files
        if (is_dir($this->testDir)) {
            array_map('unlink', glob("{$this->testDir}/*.*"));
            rmdir($this->testDir);
        }
    }

    public function testGetMarshallCreatesNewFileWithMakeMarshal(): void
    {
        $filename = "{$this->testDir}/test";
        $marshal = JsonAdapter::getMarshall($filename);

        $this->assertInstanceOf(MakeMarshal::class, $marshal);
        $this->assertFileExists("$filename.json");
    }

    public function testGetMarshallReturnsEditMarshalForExistingFile(): void
    {
        $filename = "{$this->testDir}/existing.json";
        touch($filename);
        file_put_contents($filename, json_encode(['key' => 'value']));

        $marshal = JsonAdapter::getMarshall($filename);

        $this->assertInstanceOf(EditMarshal::class, $marshal);
    }

    public function testNameFileAppendJsonExtension(): void
    {
        $filename = "{$this->testDir}/test";
        $marshal = JsonAdapter::getMarshall($filename);

        // File should be created with .json extension
        $this->assertFileExists("$filename.json");
    }

    public function testNameFileDoesNotDuplicateJsonExtension(): void
    {
        $filename = "{$this->testDir}/test.json";
        $marshal = JsonAdapter::getMarshall($filename);

        // File should not be created as test.json.json
        $this->assertFileExists($filename);
        $this->assertFileDoesNotExist("$filename.json");
    }
}
