<?php

declare(strict_types=1);

namespace BrowserConsole\Tests\Unit;

use BrowserConsole\BrowserConsole;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;

class BrowserConsoleTest extends TestCase
{
    private function captureOutput(callable $callback): string
    {
        ob_start();
        $callback();
        return ob_get_clean() ?: '';
    }

    public function testLoggerImplementsPsrLogInterface(): void
    {
        $logger = new BrowserConsole();

        $this->assertInstanceOf(\Psr\Log\LoggerInterface::class, $logger);
    }

    public function testLogWithInfoLevel(): void
    {
        $logger = new BrowserConsole();

        $output = $this->captureOutput(function () use ($logger) {
            $logger->log(LogLevel::INFO, 'Hello');
        });

        $this->assertStringContainsString('console.info("Hello")', $output);
    }

    public function testInfoMethod(): void
    {
        $logger = new BrowserConsole();

        $output = $this->captureOutput(function () use ($logger) {
            $logger->info('Hello');
        });

        $this->assertStringContainsString('console.info("Hello")', $output);
    }

    public function testErrorMethod(): void
    {
        $logger = new BrowserConsole();

        $output = $this->captureOutput(function () use ($logger) {
            $logger->error('Achtung!');
        });

        $this->assertStringContainsString('console.error("Achtung!")', $output);
    }

    public function testAlertMethod(): void
    {
        $logger = new BrowserConsole();

        $output = $this->captureOutput(function () use ($logger) {
            $logger->alert('Alert message');
        });

        $this->assertStringContainsString('console.alert("Alert message")', $output);
    }

    public function testWarningMethod(): void
    {
        $logger = new BrowserConsole();

        $output = $this->captureOutput(function () use ($logger) {
            $logger->warning('Warning message');
        });

        $this->assertStringContainsString('console.warning("Warning message")', $output);
    }

    public function testDebugMethod(): void
    {
        $logger = new BrowserConsole();

        $output = $this->captureOutput(function () use ($logger) {
            $logger->debug('Debug message');
        });

        $this->assertStringContainsString('console.debug("Debug message")', $output);
    }

    public function testEmptyMessage(): void
    {
        $logger = new BrowserConsole();

        $output = $this->captureOutput(function () use ($logger) {
            $logger->alert('');
        });

        $this->assertStringContainsString('console.alert("")', $output);
    }

    public function testContextInterpolation(): void
    {
        $logger = new BrowserConsole();

        $output = $this->captureOutput(function () use ($logger) {
            $logger->info('Hello {user}', ['user' => 'Mustermann']);
        });

        $this->assertStringContainsString('console.info("Hello Mustermann")', $output);
    }

    public function testContextInterpolationWithMultiplePlaceholders(): void
    {
        $logger = new BrowserConsole();

        $output = $this->captureOutput(function () use ($logger) {
            $logger->info('{greeting} {name}, welcome to {place}!', [
                'greeting' => 'Hello',
                'name' => 'John',
                'place' => 'Earth',
            ]);
        });

        $this->assertStringContainsString('console.info("Hello John, welcome to Earth!")', $output);
    }

    public function testInvalidLevelDefaultsToInfo(): void
    {
        $logger = new BrowserConsole();

        $output = $this->captureOutput(function () use ($logger) {
            $logger->log('invalid_level', 'Test');
        });

        $this->assertStringContainsString('console.info("Test")', $output);
    }

    public function testShowWithString(): void
    {
        $output = $this->captureOutput(function () {
            BrowserConsole::show('Hello');
        });

        $this->assertStringContainsString('console.log("Hello")', $output);
    }

    public function testShowWithArray(): void
    {
        $output = $this->captureOutput(function () {
            BrowserConsole::show(['foo' => 'bar']);
        });

        $this->assertStringContainsString('console.log({"foo":"bar"})', $output);
    }

    public function testShowWithNestedArray(): void
    {
        $output = $this->captureOutput(function () {
            BrowserConsole::show(['user' => ['name' => 'John', 'age' => 30]]);
        });

        $this->assertStringContainsString('console.log({"user":{"name":"John","age":30}})', $output);
    }

    public function testShowWithInteger(): void
    {
        $output = $this->captureOutput(function () {
            BrowserConsole::show(-100500);
        });

        $this->assertStringContainsString('console.log(-100500)', $output);
    }

    public function testShowWithFloat(): void
    {
        $output = $this->captureOutput(function () {
            BrowserConsole::show(3.14159);
        });

        $this->assertStringContainsString('console.log(3.14159)', $output);
    }

    public function testShowWithBoolean(): void
    {
        $output = $this->captureOutput(function () {
            BrowserConsole::show(true);
        });

        $this->assertStringContainsString('console.log(true)', $output);
    }

    public function testShowWithFalse(): void
    {
        $output = $this->captureOutput(function () {
            BrowserConsole::show(false);
        });

        $this->assertStringContainsString('console.log(false)', $output);
    }

    public function testShowWithNull(): void
    {
        $output = $this->captureOutput(function () {
            BrowserConsole::show(null);
        });

        $this->assertStringContainsString('console.log(null)', $output);
    }

    public function testShowWithContext(): void
    {
        $output = $this->captureOutput(function () {
            BrowserConsole::show('Hello {user}', ['user' => 'Mustermann']);
        });

        $this->assertStringContainsString('console.log("Hello Mustermann")', $output);
    }

    public function testShowWithDate(): void
    {
        $output = $this->captureOutput(function () {
            BrowserConsole::show('Hello', [], true);
        });

        $this->assertStringContainsString('] Hello', $output);
        $this->assertMatchesRegularExpression('/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\]/', $output);
    }

    public function testShowWithDateDoesNotAffectArrays(): void
    {
        $output = $this->captureOutput(function () {
            BrowserConsole::show(['foo' => 'bar'], [], true);
        });

        // Date should not be prepended to arrays
        $this->assertStringContainsString('console.log({"foo":"bar"})', $output);
        $this->assertStringNotContainsString('[20', $output);
    }

    public function testEnabledByDefault(): void
    {
        $logger = new BrowserConsole();

        $this->assertTrue($logger->isEnabled());
    }

    public function testCanBeDisabled(): void
    {
        $logger = new BrowserConsole(false);

        $this->assertFalse($logger->isEnabled());
    }

    public function testSetEnabled(): void
    {
        $logger = new BrowserConsole();
        $logger->setEnabled(false);

        $this->assertFalse($logger->isEnabled());
    }

    public function testDisabledProducesNoOutput(): void
    {
        $logger = new BrowserConsole(false);

        $output = $this->captureOutput(function () use ($logger) {
            $logger->info('This should not appear');
        });

        $this->assertEmpty(trim($output));
    }

    public function testOutputContainsScriptTag(): void
    {
        $logger = new BrowserConsole();

        $output = $this->captureOutput(function () use ($logger) {
            $logger->info('Test');
        });

        $this->assertStringContainsString('<script>', $output);
        $this->assertStringContainsString('</script>', $output);
    }

    public function testUnicodeSupport(): void
    {
        $logger = new BrowserConsole();

        $output = $this->captureOutput(function () use ($logger) {
            $logger->info('Hello 世界! 🌍');
        });

        $this->assertStringContainsString('Hello 世界! 🌍', $output);
    }

    public function testSpecialCharactersAreEscaped(): void
    {
        $logger = new BrowserConsole();

        $output = $this->captureOutput(function () use ($logger) {
            $logger->info('Line1\nLine2');
        });

        $this->assertStringContainsString('"Line1\\\\nLine2"', $output);
    }

    public function testStringableObject(): void
    {
        $stringable = new class implements \Stringable {
            public function __toString(): string
            {
                return 'Stringable content';
            }
        };

        $output = $this->captureOutput(function () use ($stringable) {
            BrowserConsole::show($stringable);
        });

        $this->assertStringContainsString('console.log("Stringable content")', $output);
    }

    public function testObjectWithToArrayMethod(): void
    {
        $object = new class {
            public function toArray(): array
            {
                return ['id' => 1, 'name' => 'Test'];
            }
        };

        $output = $this->captureOutput(function () use ($object) {
            BrowserConsole::show($object);
        });

        $this->assertStringContainsString('console.log({"id":1,"name":"Test"})', $output);
    }
}
