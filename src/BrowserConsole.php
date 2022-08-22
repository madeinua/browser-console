<?php
namespace BrowserConsole;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

class BrowserConsole extends AbstractLogger
{
    /**
     * Interpolates context values into the message placeholders.
     */
    protected function interpolate($message, array $context = [])
    {
        $replace = [];

        foreach ($context as $key => $val) {
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            }
        }

        return strtr($message, $replace);
    }

    /**
     * @return array
     */
    protected function getLogLevels(): array
    {
        return array_merge(['log'], array_values(
            (new \ReflectionClass(LogLevel::class))->getConstants()
        ));
    }


    /**
     * @param string $level
     * @param string|\Stringable $message
     * @param array $context
     */
    public function log($level, string|\Stringable $message, array $context = []): void
    {
        $message = empty($message)
            ? '""'
            : json_encode(
                $message instanceof \Stringable ? $message->__toString() : $message
            );

        if (!empty($context)) {
            $message = $this->interpolate($message, $context);
        }

        $level = in_array($level, $this->getLogLevels()) ? $level : LogLevel::INFO;

        echo PHP_EOL . "<script type='text/javascript'>if (window.console) { console.{$level}({$message}) }</script>";
    }

    /**
     * @param $message
     * @param bool $showDate
     * @param array $context
     */
    public static function show($message, array $context = [], bool $showDate = false)
    {
        if ($message instanceof \Stringable) {
            $message = $message->__toString();
        } elseif (!is_string($message)) {
            $message = json_encode($message);
        }

        if ($showDate) {
            $message = '[' . date('Y-m-d H:i:s') . '] ' . $message;
        }

        (new self())->log('log', $message, $context);
    }
}