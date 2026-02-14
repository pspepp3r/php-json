# Contributing Guide

Thank you for your interest in contributing to PHP JSON! This document provides guidelines for contributing to the project.

## Table of Contents

- [Code of Conduct](#code-of-conduct)
- [Getting Started](#getting-started)
- [Development Setup](#development-setup)
- [Making Changes](#making-changes)
- [Testing](#testing)
- [Code Standards](#code-standards)
- [Commit Guidelines](#commit-guidelines)
- [Pull Requests](#pull-requests)
- [Reporting Issues](#reporting-issues)

## Code of Conduct

Be respectful, inclusive, and professional in all interactions. We are committed to providing a welcoming and inspiring community for all.

## Getting Started

1. Fork the repository
2. Clone your fork: `git clone https://github.com/your-username/php-json.git`
3. Add upstream remote: `git remote add upstream https://github.com/pspepp3r/php-json.git`
4. Create a feature branch: `git checkout -b feature/your-feature-name`

## Development Setup

### Requirements

- PHP 8.0 or higher
- Composer
- Git

### Installation

```bash
# Clone repository
git clone https://github.com/your-username/php-json.git
cd php-json

# Install dependencies
composer install

# Run tests
php vendor/bin/phpunit
```

## Making Changes

### Project Structure

```md
src/
├── Adapters/ # Factory implementations
├── Contracts/ # Interfaces and abstract classes
├── Marshals/ # Core marshal classes
├── Errors/ # Exception classes
└── Traits/ # Reusable traits

tests/
├── Adapters/ # Adapter tests
├── Marshals/ # Marshal tests
└── Integration/ # End-to-end tests

docs/
├── API.md # API documentation
└── TESTING.md # Testing guide
```

### Adding Features

1. **Identify the component**: Determine which class needs modification
2. **Check existing tests**: Look for related test files
3. **Write test first**: Follow TDD principles
4. **Implement feature**: Make the test pass
5. **Refactor if needed**: Clean up the implementation
6. **Update documentation**: Add/update relevant docs

### Bug Fixes

1. **Create test that reproduces bug**: Demonstrates the issue
2. **Fix the bug**: Make the test pass
3. **Verify related tests**: Ensure no regressions
4. **Update docs if needed**: Document the fix

## Testing

### Run All Tests

```bash
php vendor/bin/phpunit
```

### Run Specific Test

```bash
php vendor/bin/phpunit tests/Adapters/JsonAdapterTest.php
```

### Run with Coverage

```bash
XDEBUG_MODE=coverage php vendor/bin/phpunit
```

### Test Requirements

- All new code must have tests
- Tests must pass locally before submitting PR
- Test coverage should not decrease
- Tests should be deterministic (no random failures)

### Writing Tests

Follow the existing test structure:

```php
<?php

declare(strict_types=1);

namespace PhpJson\Tests\YourNamespace;

use PHPUnit\Framework\TestCase;

final class YourClassTest extends TestCase
{
    protected function setUp(): void
    {
        // Setup
    }

    protected function tearDown(): void
    {
        // Cleanup
    }

    public function testDescriptiveMethodName(): void
    {
        // Arrange
        $object = new YourClass();

        // Act
        $result = $object->method();

        // Assert
        $this->assertTrue($result);
    }
}
```

## Code Standards

### PHP Standards

- **PHP Version**: PHP 8.0+
- **Declare Types**: Always use `declare(strict_types=1);`
- **Namespacing**: PSR-4 autoloading in `PhpJson\` namespace

### Style Guide

```php
<?php

declare(strict_types=1);

namespace PhpJson\Example;

use PhpJson\SomeClass;

/**
 * Descriptive class documentation.
 */
final class MyClass
{
    /**
     * Property documentation.
     */
    private string $property;

    /**
     * Constructor documentation.
     */
    public function __construct(string $param)
    {
        $this->property = $param;
    }

    /**
     * Method documentation.
     */
    public function myMethod(string $param): string
    {
        return $param;
    }
}
```

### Code Guidelines

1. **Use strict types**: `declare(strict_types=1);` in all files
2. **Type hints**: Always use type hints for parameters and return types
3. **Final classes**: Use `final` for concrete classes
4. **Named constants**: Define magic values as constants
5. **Documentation**: Add PHPDoc comments for public methods
6. **Error handling**: Throw appropriate exceptions

### Naming Conventions

- **Classes**: PascalCase (`MyClass`)
- **Methods**: camelCase (`myMethod()`)
- **Properties**: camelCase (`$myProperty`)
- **Constants**: UPPERCASE (`MY_CONSTANT`)
- **Namespaces**: PascalCase (`PhpJson\Example`)

### Code Quality

- Keep methods small and focused
- Limit line length to 100 characters where reasonable
- Avoid code duplication
- Use early returns to reduce nesting
- Keep cyclomatic complexity low

### Array Style

```php
// Use short array syntax
$array = ['key' => 'value'];

// Multi-line for complex arrays
$array = [
    'key1' => 'value1',
    'key2' => 'value2',
];
```

### Function Calls

```php
// Single line
json_decode($data);

// Multi-line for many parameters
someFunction(
    $param1,
    $param2,
    $param3
);
```

## Commit Guidelines

### Commit Message Format

```md
[TYPE] Brief description (50 chars max)

Detailed explanation of the changes, why they were made, and any
relevant context. Wrap at 72 characters.

Fixes #123
Relates to #456
```

### Commit Types

- `[FEATURE]` - New functionality
- `[BUGFIX]` - Bug fix
- `[DOCS]` - Documentation changes
- `[TEST]` - Test additions/modifications
- `[REFACTOR]` - Code refactoring
- `[STYLE]` - Code style changes
- `[CHORE]` - Build, dependencies, etc.

### Good Commit Practices

- Commit logically related changes together
- One feature or fix per commit
- Write clear commit messages
- Reference issues when applicable
- Avoid committing generated files
- Keep commits focused and atomic

## Pull Requests

### Before Submitting

- [ ] Code follows style guide
- [ ] Tests pass locally: `php vendor/bin/phpunit`
- [ ] New tests added for new functionality
- [ ] Documentation updated
- [ ] Commit messages are clear
- [ ] Branch is up to date with main

### PR Title Format

```md
[TYPE] Brief description of changes
```

Examples:

- `[FEATURE] Add support for nested array removal`
- `[BUGFIX] Fix file extension handling`
- `[DOCS] Update API documentation`

### PR Description Template

```markdown
## Description

Brief description of what this PR does.

## Changes

- Change 1
- Change 2
- Change 3

## Testing

- [x] Tests pass locally
- [x] New tests added
- [x] Edge cases covered

## Related Issues

Fixes #123
Relates to #456

## Breaking Changes

None / Description of breaking changes

## Notes

Any additional context or notes.
```

### Review Process

1. **Automated checks**: Tests must pass
2. **Code review**: At least one maintainer review
3. **Approval**: PR must be approved before merging
4. **Merge**: Squash and merge to main branch

### Addressing Feedback

- Respond to all comments
- Make requested changes in new commits
- Explain any disagreements respectfully
- Request re-review after changes

## Reporting Issues

### Before Creating an Issue

- Search existing issues to avoid duplicates
- Check the documentation
- Try the latest version

### Issue Guidelines

### Bug Reports

Include:

- PHP version
- Steps to reproduce
- Expected behavior
- Actual behavior
- Error messages/stack traces

**Template:**

```markdown
## Description

Brief description of the bug.

## Steps to Reproduce

1. Step 1
2. Step 2
3. Step 3

## Expected Behavior

What should happen.

## Actual Behavior

What actually happens.

## Environment

- PHP Version: 8.x
- OS: Linux/Mac/Windows
- PHP JSON Version: 1.0

## Error/Output
```

Any error messages or output

```md

```

### Feature Requests

Include:

- Use case description
- Proposed solution
- Alternative solutions considered

**Template:**

```markdown
## Description

Detailed description of the feature request.

## Use Case

Why this feature would be useful.

## Proposed Solution

How this should work.

## Alternatives

Any alternative approaches considered.

## Additional Context

Any other relevant information.
```

## Development Tips

### Useful Commands

```bash
# Run tests
php vendor/bin/phpunit

# Run tests with coverage
XDEBUG_MODE=coverage php vendor/bin/phpunit

# Run specific test file
php vendor/bin/phpunit tests/Adapters/JsonAdapterTest.php

# Run tests matching pattern
php vendor/bin/phpunit --filter testAdd

# List all available tests
php vendor/bin/phpunit --list-tests

# Run tests with verbose output
php vendor/bin/phpunit --testdox
```

### Local Development

```bash
# Keep your fork in sync
git fetch upstream
git rebase upstream/main

# Push to your fork
git push origin feature/your-feature-name

# Create pull request on GitHub
```

### Code Review Checklist

Before submitting, review:

- [ ] Code is readable and well-commented
- [ ] No hardcoded values (use constants)
- [ ] Error handling is appropriate
- [ ] No unnecessary code duplication
- [ ] Variable names are descriptive
- [ ] Functions have single responsibility
- [ ] No breaking changes (or documented)
- [ ] Documentation is accurate

## Questions?

- Check existing issues and discussions
- Review the documentation
- Ask in a new issue with `[QUESTION]` tag

## License

By contributing, you agree that your contributions will be licensed under the MIT License.

---

Thank you for contributing to PHP JSON!
