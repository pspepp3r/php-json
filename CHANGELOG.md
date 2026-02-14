# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2024-02-14

### Added

- Initial release of PHP JSON library
- `JsonAdapter` factory class for file operations
- `MakeMarshal` for creating new JSON files
- `EditMarshal` for modifying existing JSON files
- `ReadMarshal` for reading JSON files in various formats
- Support for fluent interface and method chaining
- Automatic JSON file extension handling
- Complex type support (ArrayAccess, IteratorAggregate, Traversable)
- Type hydration for PHP objects from JSON
- Nested key notation for deep operations (e.g., `address[city]`)
- Comprehensive PHPUnit test suite with 29 tests
- Full API documentation
- Testing guide with best practices
- Contributing guidelines
- README with examples and quick start guide

### Features

- CRUD operations on JSON files
- Fluent API for readable code
- Support for objects, arrays, and nested structures
- Automatic type conversion
- Pretty-printed JSON output
- Unicode support in JSON encoding
- Proper error handling with descriptive exceptions

### Documentation

- README.md - Quick start and usage guide
- docs/API.md - Detailed API reference
- docs/TESTING.md - Comprehensive testing guide
- CONTRIBUTING.md - Contribution guidelines

### Testing

- 29 comprehensive tests covering:
  - Adapter functionality (8 tests)
  - Marshal creation (7 tests)
  - Marshal modification (10 tests)
  - Marshal reading (6 tests)
  - Integration workflows (5 tests)
- Full test isolation using temporary files
- Proper setUp/tearDown lifecycle
- Exception testing
- Edge case coverage

### Development

- PHPUnit 13.0 configuration
- PHP 8.0+ type declarations
- Strict types enforcement
- PSR-4 autoloading
- No external dependencies (testing only)

## [Unreleased]

### Planned Features

- Batch operations for performance
- JSON schema validation
- Data transformation middleware
- Query interface for complex selections
- Async file operations
- Performance benchmarking suite

---

## Version Information

- **PHP Version**: 8.0+
- **License**: MIT
- **Repository**: <https://github.com/pspepp3r/php-json>
- **Package**: pspepp3r/php-json
