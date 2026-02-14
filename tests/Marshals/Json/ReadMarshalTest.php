<?php

declare(strict_types=1);

namespace PhpJson\Tests\Marshals\Json;

use PHPUnit\Framework\TestCase;
use PhpJson\Marshals\Json\ReadMarshal;

final class ReadMarshalTest extends TestCase
{
    private string $testFile;

    protected function setUp(): void
    {
        $this->testFile = sys_get_temp_dir() . '/read-test-' . uniqid() . '.json';

        $data = [
            'name' => 'John',
            'age' => 30,
            'email' => 'john@example.com'
        ];

        file_put_contents($this->testFile, json_encode($data));
    }

    protected function tearDown(): void
    {
        if (file_exists($this->testFile)) {
            unlink($this->testFile);
        }
    }

    public function testToArray(): void
    {
        $marshal = new ReadMarshal($this->testFile);
        $array = $marshal->toArray();

        $this->assertIsArray($array);
        $this->assertEquals('John', $array['name']);
        $this->assertEquals(30, $array['age']);
    }

    public function testToObject(): void
    {
        $marshal = new ReadMarshal($this->testFile);
        $object = $marshal->toObject();

        $this->assertIsObject($object);
        $this->assertEquals('John', $object->name);
        $this->assertEquals(30, $object->age);
    }

    public function testToJson(): void
    {
        $marshal = new ReadMarshal($this->testFile);
        $json = $marshal->toJson();

        $this->assertIsString($json);

        $decoded = json_decode($json, true);
        $this->assertEquals('John', $decoded['name']);
        $this->assertEquals(30, $decoded['age']);
    }

    public function testToCollection(): void
    {
        $marshal = new ReadMarshal($this->testFile);
        $collection = $marshal->toCollection();

        $this->assertIsObject($collection);
        $this->assertEquals('John', $collection->name);
    }

    public function testToDataObjectWithMatchingClass(): void
    {
        // Create a test file with matching data
        $testFile = sys_get_temp_dir() . '/user-test-' . uniqid() . '.json';
        $data = [
            'name' => 'Jane',
            'email' => 'jane@example.com',
            'age' => 25
        ];
        file_put_contents($testFile, json_encode($data));

        $marshal = new ReadMarshal($testFile);
        $user = $marshal->toDataObject(UserTestClass::class);

        $this->assertInstanceOf(UserTestClass::class, $user);
        $this->assertEquals('Jane', $user->name);

        unlink($testFile);
    }

    public function testToDataObjectWithMismatchThrowsException(): void
    {
        // This file has: name, age, email
        // UserTestClass needs: name, email, age (in constructor order)
        // Since the keys don't match exactly, it should fail
        $marshal = new ReadMarshal($this->testFile);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('don\'t match');

        $marshal->toDataObject(MismatchedUserClass::class);
    }
}

/**
 * Test class for data object conversion
 */
final class UserTestClass
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly int $age
    ) {}
}

/**
 * Mismatched test class for exception testing
 */
final class MismatchedUserClass
{
    public function __construct(
        public readonly string $username,
        public readonly string $phone
    ) {}
}
