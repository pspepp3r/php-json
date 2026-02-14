<?php

declare(strict_types=1);

namespace PhpJson\Tests\Integration;

use PhpJson\Adapters\JsonAdapter;
use PhpJson\Marshals\Json\EditMarshal;
use PhpJson\Marshals\Json\MakeMarshal;
use PHPUnit\Framework\TestCase;

final class JsonCrudOperationsTest extends TestCase
{
    private string $testDir;

    protected function setUp(): void
    {
        $this->testDir = sys_get_temp_dir() . '/php-json-crud-' . uniqid();
        mkdir($this->testDir);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->testDir)) {
            array_map('unlink', glob($this->testDir . '/*.*'));
            rmdir($this->testDir);
        }
    }

    public function testCreateNewJsonFile(): void
    {
        $filename = "{$this->testDir}/users";
        /** @var MakeMarshal */
        $marshal = JsonAdapter::getMarshall($filename);

        $marshal->addValue('id', 1)
            ->addValue('name', 'Alice')
            ->addValue('email', 'alice@example.com')
            ->store();

        $this->assertFileExists($filename . '.json');

        $content = json_decode(file_get_contents($filename . '.json'), true);
        $this->assertEquals(1, $content['id']);
        $this->assertEquals('Alice', $content['name']);
    }

    public function testReadJsonFile(): void
    {
        $filename = "{$this->testDir}/config.json";
        $data = ['app' => 'MyApp', 'version' => '1.0.0'];
        file_put_contents($filename, json_encode($data));

        /** @var EditMarshal */
        $marshal = JsonAdapter::getMarshall($filename);

        $array = $marshal->readMode()->toArray();

        $this->assertEquals('MyApp', $array['app']);
        $this->assertEquals('1.0.0', $array['version']);
    }

    public function testUpdateExistingJsonFile(): void
    {
        $filename = "{$this->testDir}/product.json";
        $data = ['id' => 1, 'name' => 'Product A', 'price' => 100];
        file_put_contents($filename, json_encode($data));

        /** @var EditMarshal */
        $marshal = JsonAdapter::getMarshall($filename);
        $marshal->setValue('price', 150)
            ->setValue('name', 'Product B')
            ->store();

        $content = json_decode(file_get_contents($filename), true);
        $this->assertEquals('Product B', $content['name']);
        $this->assertEquals(150, $content['price']);
        $this->assertEquals(1, $content['id']);
    }

    public function testDeleteValueFromJsonFile(): void
    {
        $filename = "{$this->testDir}/document.json";
        $data = ['title' => 'Doc', 'content' => 'Text', 'draft' => true];
        file_put_contents($filename, json_encode($data));

        /** @var EditMarshal */
        $marshal = JsonAdapter::getMarshall($filename);
        $marshal->removeValue('draft')
            ->store();

        $content = json_decode(file_get_contents($filename), true);
        $this->assertArrayNotHasKey('draft', $content);
        $this->assertArrayHasKey('title', $content);
        $this->assertArrayHasKey('content', $content);
    }

    public function testComplexCrudWorkflow(): void
    {
        $filename = $this->testDir . '/blog';

        // Create
        /** @var MakeMarshal */
        $create = JsonAdapter::getMarshall($filename);
        $create->addValue('title', 'My Blog')
            ->addValue('author', 'John')
            ->store();

        // Read and verify creation
        /** @var EditMarshal */
        $read = JsonAdapter::getMarshall($filename);
        $array = $read->readMode()->toArray();
        $this->assertEquals('My Blog', $array['title']);
        $this->assertEquals('John', $array['author']);

        // Update
        /** @var EditMarshal */
        $update = JsonAdapter::getMarshall($filename);
        $update->setValue('title', 'Updated Blog')
            ->setValue('author', 'Jane')
            ->store();

        // Read updated content
        /** @var EditMarshal */
        $readAgain = JsonAdapter::getMarshall($filename);
        $content = $readAgain->readMode()->toArray();
        $this->assertEquals('Updated Blog', $content['title']);
        $this->assertEquals('Jane', $content['author']);
    }
}
