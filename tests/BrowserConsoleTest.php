<?php

use BrowserConsole\BrowserConsole;

class BrowserConsoleTest extends PHPUnit\Framework\TestCase
{
    /**
     * @param callable $callback
     * @return string
     */
    private function callGetLoggerMessage(callable $callback): string
    {
        ob_start();
        $callback();
        return ob_get_clean();
    }

    public function testLogger()
    {
        $logger = new BrowserConsole();

        $this->assertStringContainsString(
            'console.info("Hello")',
            $this->callGetLoggerMessage(static function () use ($logger) {
                $logger->log(\Psr\Log\LogLevel::INFO, 'Hello');
            })
        );

        $this->assertStringContainsString(
            'console.info("Hello")',
            $this->callGetLoggerMessage(static function () use ($logger) {
                $logger->info('Hello');
            })
        );

        $this->assertStringContainsString(
            'console.error("Achtung!")',
            $this->callGetLoggerMessage(static function () use ($logger) {
                $logger->error('Achtung!');
            })
        );

        $this->assertStringContainsString(
            'console.alert("INFO")',
            $this->callGetLoggerMessage(static function () use ($logger) {
                $logger->alert('INFO');
            })
        );

        $this->assertStringContainsString(
            'console.alert("")',
            $this->callGetLoggerMessage(static function () use ($logger) {
                $logger->alert('');
            })
        );

        $this->assertStringContainsString(
            'console.log("Hello Mustermann")',
            $this->callGetLoggerMessage(static function () use ($logger) {
                $logger->show('Hello {user}', ['user' => 'Mustermann']);
            })
        );
    }

    public function testShow()
    {
        $this->assertStringContainsString(
            'console.log("Hello")',
            $this->callGetLoggerMessage(static function () {
                BrowserConsole::show('Hello');
            })
        );

        $this->assertStringContainsString(
            'console.log("{\"foo\":\"bar\"}")',
            $this->callGetLoggerMessage(static function () {
                BrowserConsole::show([
                    'foo' => 'bar'
                ]);
            })
        );

        $this->assertStringContainsString(
            'console.log("-100500")',
            $this->callGetLoggerMessage(static function () {
                BrowserConsole::show(-100500);
            })
        );

        $this->assertStringContainsString(
            'console.log("true")',
            $this->callGetLoggerMessage(static function () {
                BrowserConsole::show(true);
            })
        );

        $this->assertStringContainsString(
            'console.log("null")',
            $this->callGetLoggerMessage(static function () {
                BrowserConsole::show(null);
            })
        );

        $this->assertStringContainsString(
            '] Hello',
            $this->callGetLoggerMessage(static function () {
                BrowserConsole::show('Hello', [], true);
            })
        );
    }
}