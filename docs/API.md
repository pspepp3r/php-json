# API Documentation

## Overview

PHP JSON provides a fluent interface for CRUD operations on JSON files. The library consists of three main marshalling classes that handle different operations:

- **JsonAdapter** - Factory for getting the appropriate marshal
- **MakeMarshal** - Creates new JSON files
- **EditMarshal** - Modifies existing JSON files
- **ReadMarshal** - Reads JSON files

## JsonAdapter

### Purpose

Entry point for all JSON operations. Determines whether to create a new file or work with an existing one.

### Namespace

```php
PhpJson\Adapters\JsonAdapter
```

### Public Methods

#### `getMarshall(string $filename): Marshal`

**Description**: Returns the appropriate marshal for the given filename.

**Parameters**:

- `$filename` (string): Name of the JSON file (with or without `.json` extension)

**Returns**:

- `MakeMarshal` if the file doesn't exist
- `EditMarshal` if the file exists

**Throws**: None

**Example**:

```php
use PhpJson\Adapters\JsonAdapter;

// New file - returns MakeMarshal
$marshal = JsonAdapter::getMarshall('new_file');

// Existing file - returns EditMarshal
$marshal = JsonAdapter::getMarshall('existing_file.json');
```

**Access Modifier**: `public static`

---

## MakeMarshal

### Purpose

Creates new JSON files and initializes them with data.

### Namespace

```php
PhpJson\Marshals\Json\MakeMarshal
```

### Inheritance

Extends `PhpJson\Marshals\Json\BaseMarshal`

### Constructor

```php
public function __construct(string $filename)
```

**Parameters**:

- `$filename` (string): Full path to the JSON file to create

### Public Methods

#### `addValue(string|int $name, mixed $value): static`

**Description**: Adds a property/value pair to the JSON object.

**Parameters**:

- `$name` (string|int): Property name or collection index
- `$value` (mixed): The value to store

**Returns**: `$this` for method chaining

**Throws**: `RuntimeException` if property already exists

**Example**:

```php
$marshal = new MakeMarshal('data.json');
$marshal->addValue('name', 'John')
        ->addValue('age', 30)
        ->store();
```

#### `parseFrom(object $dataObject): void`

**Description**: Initializes JSON from a PHP object.

**Parameters**:

- `$dataObject` (object): The object to convert to JSON

**Returns**: void

**Throws**: `RuntimeException` if content already exists

**Example**:

```php
$data = new stdClass();
$data->name = 'Jane';
$data->email = 'jane@example.com';

$marshal = new MakeMarshal('user.json');
$marshal->parseFrom($data);
```

#### `store(): void`

**Description**: Persists the JSON data to file.

**Parameters**: None

**Returns**: void

**Throws**: `FileException` on write failure

**Example**:

```php
$marshal = new MakeMarshal('config.json');
$marshal->addValue('debug', true);
$marshal->store(); // File is now written
```

---

## EditMarshal

### Purpose

Modifies existing JSON files.

### Namespace

```php
PhpJson\Marshals\Json\EditMarshal
```

### Inheritance

Extends `PhpJson\Marshals\Json\BaseMarshal`

### Constructor

```php
public function __construct(string $filename)
```

**Parameters**:

- `$filename` (string): Full path to an existing JSON file

### Public Methods

#### `setValue(string|int $name, mixed $value): static`

**Description**: Updates a property or sets a new property in an existing object.

**Parameters**:

- `$name` (string|int): Property name
- `$value` (mixed): The new value

**Returns**: `$this` for method chaining

**Throws**: `RuntimeException` if property doesn't exist in object

**Example**:

```php
$marshal = new EditMarshal('user.json');
$marshal->setValue('age', 31)
        ->setValue('email', 'newemail@example.com')
        ->store();
```

#### `appendValue(string|int $name, mixed $value): static`

**Description**: Appends a value to a collection or property.

**Parameters**:

- `$name` (string|int): Property name or index
- `$value` (mixed): The value to append

**Returns**: `$this` for method chaining

**Throws**: `RuntimeException` if trying to override existing property

**Example**:

```php
$marshal = new EditMarshal('items.json');
$marshal->appendValue(0, 'new_item')
        ->store();
```

#### `removeValue(string|int $key): static`

**Description**: Removes a property from the JSON object.

**Parameters**:

- `$key` (string|int): Property name or nested key path (e.g., `'address[city]'`)

**Returns**: `$this` for method chaining

**Throws**: `RuntimeException` if key doesn't exist

**Example**:

```php
$marshal = new EditMarshal('user.json');

// Simple key removal
$marshal->removeValue('email');

// Nested key removal
$marshal->removeValue('address[city]');

$marshal->store();
```

#### `readMode(): ReadMarshal`

**Description**: Switches to read mode for retrieving data.

**Parameters**: None

**Returns**: `ReadMarshal` instance

**Throws**: None

**Example**:

```php
$marshal = new EditMarshal('user.json');
$data = $marshal->readMode()->toArray();
```

#### `store(): void`

**Description**: Persists changes to file.

**Parameters**: None

**Returns**: void

**Throws**: `FileException` on write failure

---

## ReadMarshal

### Purpose

Reads and retrieves data from JSON files in various formats.

### Namespace

```php
PhpJson\Marshals\Json\ReadMarshal
```

### Inheritance

Extends `PhpJson\Marshals\Json\BaseMarshal`

### Constructor

```php
public function __construct(string $filename)
```

**Parameters**:

- `$filename` (string): Full path to the JSON file to read

### Public Methods

#### `toArray(): array`

**Description**: Returns JSON content as a PHP array.

**Parameters**: None

**Returns**: `array` - Decoded JSON as associative array

**Throws**: None

**Example**:

```php
$marshal = new ReadMarshal('config.json');
$config = $marshal->toArray();
echo $config['app_name'];
```

#### `toObject(): object`

**Description**: Returns JSON content as `stdClass` object.

**Parameters**: None

**Returns**: `object` - Decoded JSON as stdClass

**Throws**: None

**Example**:

```php
$marshal = new ReadMarshal('user.json');
$user = $marshal->toObject();
echo $user->name;
```

#### `toJson(): string`

**Description**: Returns JSON content as JSON string.

**Parameters**: None

**Returns**: `string` - JSON encoded string

**Throws**: None

**Example**:

```php
$marshal = new ReadMarshal('data.json');
$json = $marshal->toJson();
header('Content-Type: application/json');
echo $json;
```

#### `toCollection(): stdClass`

**Description**: Returns the internal collection object (alias for toObject).

**Parameters**: None

**Returns**: `object` - The decoded JSON

**Throws**: None

**Example**:

```php
$marshal = new ReadMarshal('collection.json');
$collection = $marshal->toCollection();
```

#### `toDataObject(string $class): object`

**Description**: Converts JSON to a typed PHP object using reflection.

**Parameters**:

- `$class` (string): Fully qualified class name

**Returns**: `object` - Instance of the specified class

**Throws**: `RuntimeException` if JSON structure doesn't match class constructor parameters

**Example**:

```php
final class User {
    public function __construct(
        public readonly string $name,
        public readonly string $email
    ) {}
}

$marshal = new ReadMarshal('user.json');
$user = $marshal->toDataObject(User::class);
echo $user->name;
```

---

## Exception Classes

### FileException

**Namespace**: `PhpJson\Errors\FileException`

**Extends**: `RuntimeException`

**Description**: Thrown when file operations fail (read/write errors).

### RuntimeException

**Description**: Thrown for logical errors in CRUD operations:

| Message                                                   | Condition                                     |
| --------------------------------------------------------- | --------------------------------------------- |
| `"This property is already set. Please check your pipe!"` | Duplicate property in MakeMarshal             |
| `"This property is not set."`                             | Setting non-existent property in EditMarshal  |
| `"This overrides the property {name}."`                   | Appending to existing property in EditMarshal |
| `"This value does not exist."`                            | Removing non-existent key or nested path      |
| `"The Data Object and this JSON Object don't match"`      | Class hydration mismatch                      |

---

## Method Chaining

Most modification methods return `$this`, enabling fluent interface patterns:

```php
$marshal->setValue('key1', 'value1')
        ->setValue('key2', 'value2')
        ->removeValue('key3')
        ->store();
```

---

## File Naming Convention

- Extensions: `.json` is automatically appended if not provided
- Duplicate prevention: `"file.json"` won't become `"file.json.json"`
- Examples:
  - `JsonAdapter::getMarshall('users')` → `users.json`
  - `JsonAdapter::getMarshall('users.json')` → `users.json`

---

## JSON Encoding Options

When storing data, the following JSON encoding flags are used:

```php
JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS |
JSON_INVALID_UTF8_IGNORE | JSON_INVALID_UTF8_SUBSTITUTE |
JSON_NUMERIC_CHECK | JSON_PARTIAL_OUTPUT_ON_ERROR |
JSON_PRESERVE_ZERO_FRACTION | JSON_PRETTY_PRINT |
JSON_UNESCAPED_LINE_TERMINATORS | JSON_UNESCAPED_SLASHES |
JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
```

This ensures:

- Pretty-printed output
- Unicode support
- Numeric string detection
- UTF-8 validation
- Exception throwing on errors

---

## Data Type Support

### Supported Types

- Scalars: `string`, `int`, `float`, `bool`, `null`
- Collections: `array`, `stdClass`
- Objects implementing:
  - `ArrayAccess`
  - `IteratorAggregate`
  - `Traversable`
- Nested structures of the above types

### Type Conversion

When using `toDataObject()`:

- JSON properties are matched to constructor parameters
- Parameter names must exactly match JSON property names
- All required parameters must be present in JSON

---

## Performance Considerations

1. **File I/O**: Each `store()` call writes to disk; batch operations efficiently
2. **Large Files**: Loading very large JSON files creates in-memory copies
3. **Repeated Operations**: Consider caching marshal instances for multiple operations
4. **Serialization**: Complex object serialization may impact performance

---

## Thread Safety

The library is not thread-safe. For multi-threaded environments:

- Use file locking mechanisms externally
- Avoid concurrent writes to the same file
- Consider using external synchronization primitives

---

## Version Information

- **Current Version**: 1.0
- **PHP Version**: 8.0+
- **Dependencies**: None (standard library only)
