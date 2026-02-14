# Quick Reference

Fast lookup guide for common PHP JSON operations.

## Installation & Setup

```bash
composer require pspepp3r/php-json
php vendor/bin/phpunit  # Verify installation
```

## Common Operations

### Create New JSON File

```php
use PhpJson\Adapters\JsonAdapter;

$m = JsonAdapter::getMarshall('users');
$m->addValue('id', 1)
  ->addValue('name', 'John')
  ->store();
```

### Read JSON File

```php
$m = JsonAdapter::getMarshall('users.json');
$data = $m->readMode()->toArray();
```

### Update JSON File

```php
$m = JsonAdapter::getMarshall('users.json');
$m->setValue('name', 'Jane')
  ->store();
```

### Delete from JSON File

```php
$m = JsonAdapter::getMarshall('users.json');
$m->removeValue('email')
  ->store();
```

## API Quick Lookup

| Class         | Method           | Purpose                 |
| ------------- | ---------------- | ----------------------- |
| `JsonAdapter` | `getMarshall()`  | Get marshal for file    |
| `MakeMarshal` | `addValue()`     | Add property (new file) |
| `MakeMarshal` | `parseFrom()`    | Create from object      |
| `MakeMarshal` | `store()`        | Write to file           |
| `EditMarshal` | `setValue()`     | Update property         |
| `EditMarshal` | `appendValue()`  | Append to property      |
| `EditMarshal` | `removeValue()`  | Remove property         |
| `EditMarshal` | `readMode()`     | Switch to read mode     |
| `EditMarshal` | `store()`        | Write changes           |
| `ReadMarshal` | `toArray()`      | Get as array            |
| `ReadMarshal` | `toObject()`     | Get as object           |
| `ReadMarshal` | `toJson()`       | Get as JSON string      |
| `ReadMarshal` | `toDataObject()` | Hydrate typed object    |

## Chaining Pattern

```php
$marshal->addValue('a', 1)
        ->addValue('b', 2)
        ->addValue('c', 3)
        ->store();
```

## Nested Key Notation

```php
$marshal->removeValue('address[city]')      // Remove nested
$marshal->removeValue('items[0]')           // Remove array element
$marshal->removeValue('data[user][name]')   // Deep nesting
```

## Type Conversions

```php
// JSON to Array
$array = $marshal->readMode()->toArray();

// JSON to Object
$obj = $marshal->readMode()->toObject();

// JSON to String
$json = $marshal->readMode()->toJson();

// JSON to Typed Object
class User {
    public function __construct(
        public string $name,
        public string $email
    ) {}
}
$user = $marshal->readMode()->toDataObject(User::class);
```

## Error Handling

```php
try {
    $marshal->addValue('duplicate', 'value1')
            ->addValue('duplicate', 'value2');
} catch (RuntimeException $e) {
    echo $e->getMessage();
    // "This property is already set. Please check your pipe!"
}
```

## File Extensions

```php
// Auto-adds .json
JsonAdapter::getMarshall('users')      // Creates: users.json

// No duplication
JsonAdapter::getMarshall('config.json') // Creates: config.json

// Works with paths
JsonAdapter::getMarshall('data/users')  // Creates: data/users.json
```

## Test Running

```bash
# All tests
php vendor/bin/phpunit

# Specific test file
php vendor/bin/phpunit tests/Adapters/JsonAdapterTest.php

# With testdox
php vendor/bin/phpunit --testdox

# Specific test method
php vendor/bin/phpunit --filter testAddValue
```

## Documentation

- **README.md** - Main documentation, quick start, examples
- **docs/API.md** - Complete API reference
- **docs/TESTING.md** - Testing guide and best practices
- **CONTRIBUTING.md** - Contribution guidelines
- **CHANGELOG.md** - Version history

## Common Patterns

### Configuration Management

```php
$config = JsonAdapter::getMarshall('config');
$config->addValue('app', 'MyApp')
       ->addValue('debug', false)
       ->store();

$current = JsonAdapter::getMarshall('config.json');
$settings = $current->readMode()->toObject();
```

### Data Management

```php
// Create
$data = JsonAdapter::getMarshall('data');
$data->addValue('version', '1.0')->store();

// Read
$marshal = JsonAdapter::getMarshall('data.json');
$info = $marshal->readMode()->toArray();

// Update
$update = JsonAdapter::getMarshall('data.json');
$update->setValue('version', '1.1')->store();
```

### Type-Safe Operations

```php
class Product {
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly float $price
    ) {}
}

$m = JsonAdapter::getMarshall('product.json');
$product = $m->readMode()->toDataObject(Product::class);
echo $product->name; // Type-safe access
```

## Performance Tips

1. **Reuse marshals** - Don't recreate for same file
2. **Chain operations** - Fewer store() calls
3. **Use appropriate type** - Use arrays for large collections
4. **Batch updates** - Group changes before store()

## Debugging

```bash
# View test output
php vendor/bin/phpunit tests/YourTest.php --testdox

# Stop on first failure
php vendor/bin/phpunit --stop-on-failure

# Debug mode
php vendor/bin/phpunit --debug
```

## License

MIT License - Free to use in any project
