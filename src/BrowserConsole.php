<?php

declare(strict_types=1);

namespace BrowserConsole;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;
use Stringable;

class BrowserConsole extends AbstractLogger
{
    private bool $enabled = true;

    private const VALID_LOG_LEVELS = [
        'log',
        LogLevel::EMERGENCY,
        LogLevel::ALERT,
        LogLevel::CRITICAL,
        LogLevel::ERROR,
        LogLevel::WARNING,
        LogLevel::NOTICE,
        LogLevel::INFO,
        LogLevel::DEBUG,
    ];

    /**
     * Create a new BrowserConsole instance.
     *
     * @param bool $enabled
     */
    public function __construct(bool $enabled = true)
    {
        $this->enabled = $enabled;
    }

    /**
     * Enable or disable output.
     *
     * @param bool $enabled
     * @return $this
     */
    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Check if output is enabled.
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Interpolates context values into the message placeholders.
     *
     * @param string $message
     * @param array<string, mixed> $context
     * @return string
     */
    protected function interpolate(string $message, array $context = []): string
    {
        $replace = [];

        foreach ($context as $key => $val) {
            if (!is_array($val) && (!is_object($val) || $val instanceof Stringable)) {
                $replace['{' . $key . '}'] = (string) $val;
            }
        }

        return strtr($message, $replace);
    }

    /**
     * @return array<int, string>
     */
    protected function getLogLevels(): array
    {
        return self::VALID_LOG_LEVELS;
    }

    /**
     * Logs with an arbitrary level (PSR-3 compliant).
     *
     * @param mixed $level
     * @param string|Stringable $message
     * @param array<string, mixed> $context
     * @return void
     */
    public function log($level, string|Stringable $message, array $context = []): void
    {
        $output = (string) $message;

        if (!empty($context)) {
            $output = $this->interpolate($output, $context);
        }

        $this->outputToConsole($level, $output);
    }

    /**
     * Output any value to the browser console.
     *
     * @param string $level
     * @param mixed $value
     * @return void
     */
    protected function outputToConsole(string $level, mixed $value): void
    {
        if (!$this->enabled) {
            return;
        }

        $jsOutput = json_encode($value, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        $level = in_array($level, $this->getLogLevels(), true) ? $level : LogLevel::INFO;

        echo PHP_EOL . "<script>console.{$level}({$jsOutput})</script>";
    }

    /**
     * Prepare a mixed message for output.
     *
     * @param mixed $message
     * @return mixed
     */
    protected function prepareMessage(mixed $message): mixed
    {
        // Convert objects with toArray() method (e.g., Laravel models, collections)
        if (is_object($message) && method_exists($message, 'toArray')) {
            return $message->toArray();
        }

        if ($message instanceof Stringable) {
            return (string) $message;
        }

        return $message;
    }

    /**
     * Static convenience method to log any value to the browser console.
     *
     * @param mixed $message
     * @param array<string, mixed> $context
     * @param bool $showDate
     * @return void
     */
    public static function show(mixed $message, array $context = [], bool $showDate = false): void
    {
        $instance = new self();
        $prepared = $instance->prepareMessage($message);

        // Interpolate context for string messages
        if (is_string($prepared) && !empty($context)) {
            $prepared = $instance->interpolate($prepared, $context);
        }

        // Only prepend date for string messages
        if ($showDate && is_string($prepared)) {
            $prepared = '[' . date('Y-m-d H:i:s') . '] ' . $prepared;
        }

        $instance->outputToConsole('log', $prepared);
    }
}
