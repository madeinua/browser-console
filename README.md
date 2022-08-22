# Browser console logger

## About

A simple PHP class to log messages directly to the browser console.

PSR-3: Logger Interface compatible.

## Installation

Just add this line to your composer.json file:

`"madeinua/browser-console": "master"`

or run

`composer require madeinua/browser-console`

## Usage

Run the PHP script:

```php
BrowserConsole\BrowserConsole::show('Hello World!');
```

Then check out the browser console for the message "Hello World!".

### Additional features

Using the context:

```php
BrowserConsole\BrowserConsole::show('Hello {user}!', ['user' => 'Mark']);

# >> Hello Mark!
```

Including the date/time:

```php
BrowserConsole\BrowserConsole::show('Hello', [], true);

# >> [2022-01-01 15:00:00] Hello
```

Using the PSR-3: Logger Interface layer:

```php
$logger = new BrowserConsole\BrowserConsole();
$logger->error('Some error');

# >> Some error

$logger->info('Info');

# >> Info

$logger->log('info', 'Info message');

# >> Info message

$logger->log('alert', 'Hi {name}.', ['name' => 'John']);

# >> Hi John.
```