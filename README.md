# PHP JSON - CRUD Operations on JSON Files

A lightweight PHP library for performing CRUD (Create, Read, Update, Delete) operations on JSON files with an intuitive, fluent API.

## Table of Contents

- [Features](#features)
- [Installation](#installation)
- [Quick Start](#quick-start)
- [Core Components](#core-components)
- [API Reference](#api-reference)
- [Testing](#testing)
- [Examples](#examples)

## Features

- **Simple API**: Fluent interface for easy JSON manipulation
- **CRUD Operations**: Create, read, update, and delete JSON data seamlessly
- **Type Support**: Work with objects, arrays, and complex nested structures
- **Data Serialization**: Convert JSON to PHP objects with automatic type hydration
- **File Management**: Automatic file creation and management
- **Well Tested**: Comprehensive test suite with PHPUnit

## Installation

This package is installed with Composer dependencies. To use it in a PHP project:

```bash
composer require pspepp3r/php-json
```

## Quick Start

### Create a New JSON File

```php
<?php

use PhpJson\Adapters\JsonAdapter;

// Create a new JSON file and add data
$marshal = JsonAdapter::getMarshall('users');

$marshal->addValue('name', 'John Doe')
        ->addValue('email', 'john@example.com')
        ->addValue('age', 30)
        ->store();

// File created as: users.json
```

### Read JSON Data

```php
<?php

use PhpJson\Adapters\JsonAdapter;

// Read an existing JSON file
$marshal = JsonAdapter::getMarshall('users.json');

// Convert to array
$data = $marshal->readMode()->toArray();
echo $data['name']; // John Doe

// Convert to object
$user = $marshal->readMode()->toObject();
echo $user->email; // john@example.com

// Get as JSON string
$json = $marshal->readMode()->toJson();
echo $json; // {"name":"John Doe",...}
```

### Update JSON Data

```php
<?php

use PhpJson\Adapters\JsonAdapter;

// Update existing file
$marshal = JsonAdapter::getMarshall('users.json');

$marshal->setValue('age', 31)
        ->setValue('email', 'newemail@example.com')
        ->store();
```

### Delete Data

```php
<?php

use PhpJson\Adapters\JsonAdapter;

// Remove properties from JSON file
$marshal = JsonAdapter::getMarshall('users.json');

$marshal->removeValue('age')
        ->removeValue('email')
        ->store();
```

## Core Components

### JsonAdapter

The main entry point for interacting with JSON files. Automatically determines whether to create a new file or work with an existing one.

```php
public static function getMarshall(string $filename): Marshal
```

- **Returns**: `MakeMarshal` for new files, `EditMarshal` for existing files
- Automatically appends `.json` extension if not provided

### MakeMarshal

Used for creating and initializing new JSON files.

#### Methods

**`addValue(string|int $name, mixed $value): static`**

- Adds a property to the JSON object
- Throws `RuntimeException` if property already exists
- Returns `$this` for method chaining

**`parseFrom(object $dataObject): void`**

- Creates JSON from a PHP object
- Automatically converts nested objects and arrays

**`store(): void`**

- Persists the JSON data to file
- Handles complex types (ArrayAccess, IteratorAggregate, etc.)

### EditMarshal

Used for modifying existing JSON files.

#### Methods

**`setValue(string|int $name, mixed $value): static`**

- Updates or sets a property value
- For new properties, throws `RuntimeException` if property doesn't exist
- Returns `$this` for method chaining

**`appendValue(string|int $name, mixed $value): static`**

- Appends a value to a collection or updates a reference
- Throws `RuntimeException` if trying to override existing properties
- Returns `$this` for method chaining

**`removeValue(string|int $key): static`**

- Removes a property from the JSON object
- Supports nested key notation: `'address[city]'`
- Throws `RuntimeException` if key doesn't exist
- Returns `$this` for method chaining

**`readMode(): ReadMarshal`**

- Switches to read mode to retrieve data
- Returns a new `ReadMarshal` instance

**`store(): void`**

- Persists changes to file

### ReadMarshal

Used for reading and retrieving data from JSON files.

#### Methods

**`toArray(): array`**

- Returns JSON content as PHP array

**`toObject(): object`**

- Returns JSON content as `stdClass` object

**`toJson(): string`**

- Returns JSON content as JSON string

**`toCollection(): stdClass`**

- Returns the internal collection object

**`toDataObject(string $class): object`**

- Hydrates JSON data into a typed PHP object
- Uses reflection to match JSON properties with constructor parameters
- Throws `RuntimeException` if JSON structure doesn't match class constructor

## API Reference

### Exception Handling

The library throws `RuntimeException` for various error conditions:

| Error                                                     | Condition                                    |
| --------------------------------------------------------- | -------------------------------------------- |
| `"This property is already set. Please check your pipe!"` | Adding duplicate property in MakeMarshal     |
| `"This property is not set."`                             | Setting non-existent property in EditMarshal |
| `"This overrides the property {name}."`                   | Appending to existing property               |
| `"This value does not exist."`                            | Removing non-existent key                    |
| `"The Data Object and this JSON Object don't match"`      | Class hydration mismatch                     |

### File Naming

- Files are automatically named with `.json` extension if not provided
- Duplicate extensions are prevented: `"file.json"` stays `"file.json"`, not `"file.json.json"`

### Method Chaining

Most methods return `$this`, allowing for fluent interface:

```php
$marshal->addValue('key1', 'value1')
        ->addValue('key2', 'value2')
        ->addValue('key3', 'value3')
        ->store();
```

## Testing

### Run Tests

```bash
# Run all tests
php vendor/bin/phpunit

# Run specific test file
php vendor/bin/phpunit tests/Adapters/JsonAdapterTest.php

# Run with verbose output
php vendor/bin/phpunit --testdox
```

### Test Structure

- `tests/Adapters/` - JsonAdapter functionality tests
- `tests/Marshals/Json/` - MakeMarshal, EditMarshal, ReadMarshal tests
- `tests/Integration/` - End-to-end CRUD workflow tests

### Test Coverage

The test suite includes:

- **8 Adapter tests** - File creation and type selection
- **7 MakeMarshal tests** - Object/collection creation and chaining
- **10 EditMarshal tests** - Update, delete, and modification operations
- **6 ReadMarshal tests** - Data retrieval and type conversion
- **5 Integration tests** - Complete CRUD workflows

## Examples

### Example 1: User Management

```php
<?php

use PhpJson\Adapters\JsonAdapter;

// Create new user file
$user = JsonAdapter::getMarshall('users/john');
$user->addValue('id', 1)
     ->addValue('name', 'John Doe')
     ->addValue('email', 'john@example.com')
     ->addValue('role', 'admin')
     ->store();

// Update user
$update = JsonAdapter::getMarshall('users/john.json');
$update->setValue('role', 'moderator')
       ->setValue('email', 'john.doe@example.com')
       ->store();

// Read user data
$read = JsonAdapter::getMarshall('users/john.json');
$userData = $read->readMode()->toArray();
echo $userData['name']; // John Doe

// Remove sensitive data
$remove = JsonAdapter::getMarshall('users/john.json');
$remove->removeValue('email')
       ->store();
```

### Example 2: Configuration Files

```php
<?php

use PhpJson\Adapters\JsonAdapter;

// Create app configuration
$config = JsonAdapter::getMarshall('config');
$config->addValue('app_name', 'MyApp')
       ->addValue('debug', false)
       ->addValue('version', '1.0.0')
       ->store();

// Update configuration
$config = JsonAdapter::getMarshall('config.json');
$config->setValue('debug', true)
       ->setValue('version', '1.1.0')
       ->store();

// Read configuration
$config = JsonAdapter::getMarshall('config.json');
$settings = $config->readMode()->toObject();
echo $settings->app_name; // MyApp
```

### Example 3: Type Conversion

```php
<?php

use PhpJson\Adapters\JsonAdapter;

// Define a data class
final class Product {
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly float $price
    ) {}
}

// Create product JSON
$product = JsonAdapter::getMarshall('products/laptop');
$product->addValue('id', 1)
        ->addValue('name', 'Gaming Laptop')
        ->addValue('price', 1299.99)
        ->store();

// Convert to typed object
$read = JsonAdapter::getMarshall('products/laptop.json');
$productObj = $read->readMode()->toDataObject(Product::class);

echo $productObj->name; // Gaming Laptop
echo $productObj->price; // 1299.99
```

### Example 4: Nested Data Operations

```php
<?php

use PhpJson\Adapters\JsonAdapter;

// Create nested structure
$order = JsonAdapter::getMarshall('orders/order123');
$order->addValue('id', 'ORD-001')
      ->addValue('customer', 'Jane Smith')
      ->parseFrom((object)[
          'items' => [
              ['id' => 1, 'qty' => 2, 'price' => 29.99],
              ['id' => 2, 'qty' => 1, 'price' => 49.99]
          ],
          'total' => 109.97
      ]);
// This will recreate the file with the new object
$order->store();

// Update nested value
$edit = JsonAdapter::getMarshall('orders/order123.json');
$edit->setValue('total', 119.97)
     ->store();

// Retrieve nested data
$read = JsonAdapter::getMarshall('orders/order123.json');
$orderData = $read->readMode()->toArray();
echo count($orderData['items']); // 2

// Remove nested value
$edit = JsonAdapter::getMarshall('orders/order123.json');
$edit->removeValue('items[0]')
     ->store();
```

## Best Practices

1. **Always call `store()`**: Changes are only persisted when you explicitly call `store()`
2. **Use chaining**: Method chaining makes code more readable and efficient
3. **Check for existence**: Always verify JSON files exist before operations
4. **Handle exceptions**: Wrap operations in try-catch blocks for production code
5. **Type safety**: Use `toDataObject()` for typed operations when possible
6. **File permissions**: Ensure your application has write permissions in the directory

## License

MIT License - See LICENSE file for details
