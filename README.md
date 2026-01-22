# Browser Console Logger

A simple PHP class to log messages directly to the browser console. PSR-3 Logger Interface compatible with Laravel integration.

## Requirements

- PHP 8.0 or higher
- PSR-3 Log (psr/log) ^2.0 or ^3.0

## Installation

```bash
composer require madeinua/browser-console
```

### Laravel Integration

The package auto-discovers in Laravel 5.5+. For older versions, add the service provider and facade to `config/app.php`:

```php
'providers' => [
    // ...
    BrowserConsole\BrowserConsoleServiceProvider::class,
],

'aliases' => [
    // ...
    'Console' => BrowserConsole\Facades\Console::class,
],
```

Publish the configuration file (optional):

```bash
php artisan vendor:publish --tag=browser-console-config
```

## Usage

### Static Method (Quick Usage)

```php
use BrowserConsole\BrowserConsole;

// Simple message
BrowserConsole::show('Hello World!');

// Output: console.log("Hello World!")
```

### Context Interpolation

```php
BrowserConsole::show('Hello {user}!', ['user' => 'Mark']);

// Output: console.log("Hello Mark!")
```

### Including Timestamp

```php
BrowserConsole::show('Hello', [], true);

// Output: console.log("[2024-01-01 15:00:00] Hello")
```

### Different Data Types

```php
// Arrays (displayed as expandable objects in browser console)
BrowserConsole::show(['name' => 'John', 'age' => 30]);
// Output: console.log({"name":"John","age":30})

// Numbers
BrowserConsole::show(42);
// Output: console.log(42)

// Booleans
BrowserConsole::show(true);
// Output: console.log(true)

// Null
BrowserConsole::show(null);
// Output: console.log(null)
```

### PSR-3 Logger Interface

```php
use BrowserConsole\BrowserConsole;

$logger = new BrowserConsole();

// Different log levels
$logger->emergency('System is down!');
$logger->alert('Alert message');
$logger->critical('Critical error');
$logger->error('Error occurred');
$logger->warning('Warning message');
$logger->notice('Notice');
$logger->info('Information');
$logger->debug('Debug info');

// With context interpolation
$logger->info('User {user} logged in', ['user' => 'John']);
// Output: console.info("User John logged in")
```

### Laravel Facade

```php
use BrowserConsole\Facades\Console;

Console::info('Information message');
Console::error('Error message');
Console::show(['data' => 'value']);
```

### Laravel Models and Collections

The logger automatically handles Laravel models and collections:

```php
use App\Models\User;

// Single model
BrowserConsole::show(User::find(1));

// Collection
BrowserConsole::show(User::all());
```

### Enable/Disable Output

```php
$logger = new BrowserConsole(enabled: false);
$logger->info('This will not output anything');

// Or toggle
$logger->setEnabled(true);
$logger->info('Now it outputs');
```

### Laravel Configuration

In your `.env` file:

```env
BROWSER_CONSOLE_ENABLED=true  # Set to false in production
```

Or in `config/browser-console.php`:

```php
return [
    'enabled' => env('BROWSER_CONSOLE_ENABLED', true),
];
```

## Output

All messages are output as JavaScript `<script>` tags:

```html
<script>console.log("Hello World!")</script>
<script>console.info("Info message")</script>
<script>console.error("Error message")</script>
```

## Testing

```bash
composer test
```

Or with PHPUnit directly:

```bash
./vendor/bin/phpunit
```

## License

MIT License. See [LICENSE](LICENSE) for details.
