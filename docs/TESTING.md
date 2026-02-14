# Testing Guide

## Overview

PHP JSON includes a comprehensive test suite using PHPUnit 13.0. The test suite covers all major functionality with 29 tests across multiple test classes.

## Running Tests

### Prerequisites

Tests require:

- PHP 8.0 or higher
- PHPUnit 13.0 (included in vendor)
- Write access to temporary directory

### Run All Tests

```bash
# From project root
php vendor/bin/phpunit
```

Expected output:

```md
PHPUnit 13.0.2 by Sebastian Bergmann and contributors.

Runtime: PHP 8.x.x
Configuration: phpunit.xml

..................... 29 / 29 (100%)

OK, but there were issues!
Tests: 29, Assertions: 62, Warnings: 1.
```

### Run Specific Test Suite

```bash
# Adapter tests only
php vendor/bin/phpunit tests/Adapters/

# Marshal tests only
php vendor/bin/phpunit tests/Marshals/

# Integration tests only
php vendor/bin/phpunit tests/Integration/
```

### Run Specific Test File

```bash
php vendor/bin/phpunit tests/Adapters/JsonAdapterTest.php
```

### Run Specific Test Method

```bash
php vendor/bin/phpunit --filter testAddValueToObject
```

### Run with TestDox Output

```bash
php vendor/bin/phpunit --testdox
```

### Run Tests with Increased Verbosity

```bash
php vendor/bin/phpunit --debug
```

## Test Structure

```md
tests/
├── Adapters/
│ └── JsonAdapterTest.php # 8 tests
├── Marshals/
│ └── Json/
│ ├── MakeMarshalTest.php # 7 tests
│ ├── EditMarshalTest.php # 10 tests
│ └── ReadMarshalTest.php # 6 tests
└── Integration/
└── JsonCrudOperationsTest.php # 5 tests
```

## Test Coverage

### Adapter Tests (JsonAdapterTest)

Tests the `JsonAdapter` factory class functionality.

| Test                                               | Purpose                                 |
| -------------------------------------------------- | --------------------------------------- |
| `testGetMarshallCreatesNewFileWithMakeMarshal`     | Verifies new files get MakeMarshal      |
| `testGetMarshallReturnsEditMarshalForExistingFile` | Verifies existing files get EditMarshal |
| `testNameFileAppendJsonExtension`                  | Checks `.json` extension is added       |
| `testNameFileDoesNotDuplicateJsonExtension`        | Prevents double extensions              |

### MakeMarshal Tests (MakeMarshalTest)

Tests JSON file creation and initialization.

| Test                                   | Purpose                       |
| -------------------------------------- | ----------------------------- |
| `testAddValueToObject`                 | Adds properties to new object |
| `testAddValueReturnsStaticForChaining` | Verifies method chaining      |
| `testParseFromObject`                  | Creates JSON from PHP object  |
| `testDuplicatePropertyThrowsException` | Prevents duplicate properties |
| `testResetDeletesFile`                 | File cleanup on errors        |

### EditMarshal Tests (EditMarshalTest)

Tests JSON file modification operations.

| Test                                           | Purpose                         |
| ---------------------------------------------- | ------------------------------- |
| `testSetValueUpdatesProperty`                  | Updates existing properties     |
| `testSetValueReturnsStaticForChaining`         | Verifies method chaining        |
| `testAppendValueToCollection`                  | Handles collection appends      |
| `testRemoveValueSimpleKey`                     | Removes top-level properties    |
| `testRemoveValueNestedKey`                     | Removes nested properties       |
| `testRemoveValueNonexistentKeyThrowsException` | Error on missing key            |
| `testReadModeReturnsReadMarshal`               | Switches to read mode           |
| `testChainedOperations`                        | Multiple operations in sequence |

### ReadMarshal Tests (ReadMarshalTest)

Tests JSON data retrieval in various formats.

| Test                                          | Purpose                   |
| --------------------------------------------- | ------------------------- |
| `testToArray`                                 | Converts JSON to array    |
| `testToObject`                                | Converts JSON to object   |
| `testToJson`                                  | Returns JSON string       |
| `testToCollection`                            | Returns collection object |
| `testToDataObjectWithMatchingClass`           | Hydrates typed objects    |
| `testToDataObjectWithMismatchThrowsException` | Error on type mismatch    |

### Integration Tests (JsonCrudOperationsTest)

Tests complete CRUD workflows.

| Test                          | Purpose                  |
| ----------------------------- | ------------------------ |
| `testCreateNewJsonFile`       | Full creation workflow   |
| `testReadJsonFile`            | Full read workflow       |
| `testUpdateExistingJsonFile`  | Full update workflow     |
| `testDeleteValueFromJsonFile` | Full delete workflow     |
| `testComplexCrudWorkflow`     | Multi-operation sequence |

## Writing New Tests

### Test Template

```php
<?php

declare(strict_types=1);

namespace PhpJson\Tests\YourNamespace;

use PHPUnit\Framework\TestCase;
use PhpJson\YourClass;

final class YourClassTest extends TestCase
{
    private string $testFile;

    protected function setUp(): void
    {
        // Setup before each test
        $this->testFile = sys_get_temp_dir() . '/test-' . uniqid() . '.json';
    }

    protected function tearDown(): void
    {
        // Cleanup after each test
        if (file_exists($this->testFile)) {
            unlink($this->testFile);
        }
    }

    public function testSomething(): void
    {
        // Arrange
        $marshal = new YourClass($this->testFile);

        // Act
        $result = $marshal->doSomething();

        // Assert
        $this->assertTrue($result);
    }
}
```

### Best Practices

1. **Use descriptive names**: `testAddsPropertyToNewObject()` not `testAdd()`
2. **One assertion per test**: Easier to debug failures
3. **Arrange-Act-Assert**: Clear test structure
4. **Cleanup resources**: Always clean temp files in `tearDown()`
5. **Test edge cases**: Empty values, null, exceptions
6. **Use fixtures**: Create reusable test data

### Common Assertions

```php
// Basic assertions
$this->assertTrue($value);
$this->assertFalse($value);
$this->assertNull($value);
$this->assertEmpty($array);

// Equality
$this->assertEquals($expected, $actual);
$this->assertSame($expected, $actual); // strict comparison

// Type checking
$this->assertIsArray($value);
$this->assertIsObject($value);
$this->assertIsString($value);
$this->assertInstanceOf(ClassName::class, $object);

// File assertions
$this->assertFileExists($path);
$this->assertFileDoesNotExist($path);

// Container assertions
$this->assertArrayHasKey('key', $array);
$this->assertArrayNotHasKey('key', $array);
$this->assertContains($value, $array);

// Exception assertions
$this->expectException(ExceptionClass::class);
$this->expectExceptionMessage('message');
```

## Test Data

### Temporary Files

Tests use `sys_get_temp_dir()` for file operations:

```php
$testFile = sys_get_temp_dir() . '/test-' . uniqid() . '.json';
```

Benefits:

- Cross-platform compatibility
- Automatic cleanup possible
- No pollution of project directory
- Isolated test environment

### Sample Data

Common test data structures:

```php
// Simple object
$data = ['name' => 'John', 'age' => 30];

// Nested object
$data = [
    'user' => [
        'name' => 'Jane',
        'address' => ['city' => 'NYC']
    ]
];

// Array collection
$data = ['item1', 'item2', 'item3'];
```

## Debugging Tests

### Enable Debug Output

```bash
php vendor/bin/phpunit --debug
```

### Stop on First Failure

```bash
php vendor/bin/phpunit --stop-on-failure
```

### Show Incomplete/Skipped Tests

```bash
php vendor/bin/phpunit --verbose
```

### Print Processed Configuration

```bash
php vendor/bin/phpunit --configuration-override-php-ini-set display_errors=1
```

### Manual Debugging

Add to test method:

```php
public function testSomething(): void
{
    $marshal = new EditMarshal($this->testFile);
    $result = $marshal->doSomething();

    // Print debug info
    var_dump($result);
    echo "Test file: " . $this->testFile . "\n";
}
```

Then run:

```bash
php vendor/bin/phpunit tests/YourTest.php --stop-on-failure
```

## Test Configuration

### phpunit.xml

Located at project root. Key settings:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit bootstrap="vendor/autoload.php"
         cacheDirectory=".phpunit.cache"
         failOnEmptyTestSuite="true">
    <testsuites>
        <testsuite name="PhpJson Test Suite">
            <directory>tests</directory>
        </testsuite>
    </testsuites>
</phpunit>
```

### Custom Configuration

Create `phpunit-custom.xml`:

```bash
php vendor/bin/phpunit -c phpunit-custom.xml
```

## Continuous Integration

### GitHub Actions Example

```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest

    strategy:
      matrix:
        php-version: ["8.0", "8.1", "8.2", "8.3", "8.4"]

    steps:
      - uses: actions/checkout@v2

      - uses: shivammathur/setup-php@v2
        with:
          php-version: ${{ matrix.php-version }}

      - run: composer install

      - run: php vendor/bin/phpunit
```

## Troubleshooting

### Tests Fail with "Directory Not Found"

**Issue**: Test creates file in non-existent directory

**Solution**: Create directory in setUp():

```php
protected function setUp(): void
{
    $this->testDir = sys_get_temp_dir() . '/test-' . uniqid();
    mkdir($this->testDir);
}
```

### Permission Denied Errors

**Issue**: Cannot write to temp directory

**Solution**: Check file permissions and temp directory:

```bash
php -r "echo sys_get_temp_dir();"
```

### Tests Hang or Timeout

**Issue**: Infinite loops or resource locks

**Solution**:

- Check for unclosed file handles
- Look for infinite loops in test setup
- Use `--timeout=30` to limit test time

### Assertion Failures

**Issue**: Expected value doesn't match actual

**Solution**:

- Add `--verbose` flag to see differences
- Use `var_dump()` to inspect values
- Check data type mismatches

## Performance

### Test Execution Time

Current test suite executes in ~0.3-0.6 seconds.

To profile:

```bash
php vendor/bin/phpunit --verbose
```

Look for timing information in output.

### Optimize Slow Tests

1. Reduce file I/O
2. Use in-memory mocks where possible
3. Batch related tests
4. Consider data providers for multiple test cases

## Test Maintenance

### Regular Tasks

- Review test coverage quarterly
- Update tests when API changes
- Remove obsolete tests
- Refactor duplicated test code

### When to Add Tests

- New features
- Bug fixes (add test that reproduces bug first)
- API changes
- Edge cases discovered in production

## Resources

- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [Testing Best Practices](https://phpunit.de/manual/current/en/writing-tests-for-phpunit.html)
- [Assertions Reference](https://phpunit.de/manual/current/en/appendixes.assertions.html)
