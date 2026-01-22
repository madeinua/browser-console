<?php

declare(strict_types=1);

namespace BrowserConsole\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void show(mixed $message, array $context = [], bool $showDate = false)
 * @method static void log(mixed $level, string|\Stringable $message, array $context = [])
 * @method static void emergency(string|\Stringable $message, array $context = [])
 * @method static void alert(string|\Stringable $message, array $context = [])
 * @method static void critical(string|\Stringable $message, array $context = [])
 * @method static void error(string|\Stringable $message, array $context = [])
 * @method static void warning(string|\Stringable $message, array $context = [])
 * @method static void notice(string|\Stringable $message, array $context = [])
 * @method static void info(string|\Stringable $message, array $context = [])
 * @method static void debug(string|\Stringable $message, array $context = [])
 *
 * @see \BrowserConsole\BrowserConsole
 */
class Console extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'browser-console';
    }
}
