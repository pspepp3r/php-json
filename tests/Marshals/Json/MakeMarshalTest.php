<?php

declare(strict_types=1);

namespace PhpJson\Tests\Marshals\Json;

use PHPUnit\Framework\TestCase;
use PhpJson\Marshals\Json\MakeMarshal;

final class MakeMarshalTest extends TestCase
{
    private string $testFile;

    protected function setUp(): void
    {
        $this->testFile = sys_get_temp_dir() . '/make-test-' . uniqid() . '.json';
    }

    protected function tearDown(): void
    {
        if (file_exists($this->testFile)) {
            unlink($this->testFile);
        }
    }

    public function testAddValueToObject(): void
    {
        $marshal = new MakeMarshal($this->testFile);
        $marshal->addValue('name', 'John')
            ->addValue('email', 'john@example.com')
            ->store();

        $this->assertFileExists($this->testFile);

        $content = json_decode(file_get_contents($this->testFile), true);
        $this->assertEquals('John', $content['name']);
        $this->assertEquals('john@example.com', $content['email']);
    }

    public function testAddValueReturnsStaticForChaining(): void
    {
        $marshal = new MakeMarshal($this->testFile);
        $result = $marshal->addValue('key', 'value');

        $this->assertInstanceOf(MakeMarshal::class, $result);
        $this->assertSame($marshal, $result);
    }

    public function testParseFromObject(): void
    {
        $marshal = new MakeMarshal($this->testFile);

        $data = new \stdClass();
        $data->name = 'Jane';
        $data->age = 30;

        $marshal->parseFrom($data);

        $this->assertFileExists($this->testFile);

        $content = json_decode(file_get_contents($this->testFile), true);
        $this->assertEquals('Jane', $content['name']);
        $this->assertEquals(30, $content['age']);
    }

    public function testDuplicatePropertyThrowsException(): void
    {
        $marshal = new MakeMarshal($this->testFile);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('This property is already set');

        $marshal->addValue('name', 'John')
            ->addValue('name', 'Jane');
    }

    public function testResetDeletesFile(): void
    {
        $marshal = new MakeMarshal($this->testFile);

        touch($this->testFile);
        $this->assertFileExists($this->testFile);

        // Reset through duplicate property
        try {
            $marshal->addValue('key', 'value1')
                ->addValue('key', 'value2');
        } catch (\RuntimeException) {
            // Expected
        }

        $this->assertFileDoesNotExist($this->testFile);
    }
}
