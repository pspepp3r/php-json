<?php

declare(strict_types=1);

namespace PhpJson\Tests\Marshals\Json;

use PHPUnit\Framework\TestCase;
use PhpJson\Marshals\Json\EditMarshal;
use PhpJson\Marshals\Json\ReadMarshal;

final class EditMarshalTest extends TestCase
{
    private string $testFile;

    protected function setUp(): void
    {
        $this->testFile = sys_get_temp_dir() . '/edit-test-' . uniqid() . '.json';

        $data = [
            'name' => 'John',
            'email' => 'john@example.com',
            'address' => [
                'city' => 'New York',
                'zip' => '10001'
            ],
            'tags' => ['php', 'testing']
        ];

        file_put_contents($this->testFile, json_encode($data));
    }

    protected function tearDown(): void
    {
        if (file_exists($this->testFile)) {
            unlink($this->testFile);
        }
    }

    public function testSetValueUpdatesProperty(): void
    {
        $marshal = new EditMarshal($this->testFile);
        $marshal->setValue('name', 'Jane')
            ->store();

        $content = json_decode(file_get_contents($this->testFile), true);
        $this->assertEquals('Jane', $content['name']);
    }

    public function testSetValueReturnsStaticForChaining(): void
    {
        $marshal = new EditMarshal($this->testFile);
        $result = $marshal->setValue('name', 'Jane');

        $this->assertInstanceOf(EditMarshal::class, $result);
        $this->assertSame($marshal, $result);
    }

    public function testAppendValueToCollection(): void
    {
        $marshal = new EditMarshal($this->testFile);
        // appendValue appends to root collection, not nested arrays
        // This will throw because property exists
        $this->expectException(\RuntimeException::class);
        $marshal->appendValue('tags', 'laravel');
    }

    public function testAppendValueToRootArray(): void
    {
        $testFile = sys_get_temp_dir() . '/append-test-' . uniqid() . '.json';
        file_put_contents($testFile, json_encode(['item1', 'item2']));

        $marshal = new EditMarshal($testFile);
        $marshal->appendValue(0, 'item3')
            ->store();

        $content = json_decode(file_get_contents($testFile), true);
        $this->assertContains('item3', $content);

        unlink($testFile);
    }

    public function testRemoveValueSimpleKey(): void
    {
        $marshal = new EditMarshal($this->testFile);
        $marshal->removeValue('email')
            ->store();

        $content = json_decode(file_get_contents($this->testFile), true);
        $this->assertArrayNotHasKey('email', $content);
        $this->assertArrayHasKey('name', $content);
    }

    public function testRemoveValueNestedKey(): void
    {
        $marshal = new EditMarshal($this->testFile);
        $marshal->removeValue('address[city]')
            ->store();

        $content = json_decode(file_get_contents($this->testFile), true);
        $this->assertArrayNotHasKey('city', $content['address']);
        $this->assertArrayHasKey('zip', $content['address']);
    }

    public function testRemoveValueNonexistentKeyThrowsException(): void
    {
        $marshal = new EditMarshal($this->testFile);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('This value does not exist');

        $marshal->removeValue('nonexistent');
    }

    public function testReadModeReturnsReadMarshal(): void
    {
        $marshal = new EditMarshal($this->testFile);
        $readMarshal = $marshal->readMode();

        $this->assertInstanceOf(ReadMarshal::class, $readMarshal);
    }

    public function testChainedOperations(): void
    {
        $marshal = new EditMarshal($this->testFile);
        $marshal->setValue('name', 'Bob')
            ->setValue('email', 'bob@example.com')
            ->store();

        $content = json_decode(file_get_contents($this->testFile), true);
        $this->assertEquals('Bob', $content['name']);
        $this->assertEquals('bob@example.com', $content['email']);
    }
}
