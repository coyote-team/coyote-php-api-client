<?php

namespace Coyote\Traits;

use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

trait LoggerTrait
{
    public static function logInfo(string $message, $data = []): void
    {
        self::log($message, $data, Logger::INFO, get_called_class());
    }

    public static function logWarning(string $message, $data = []): void
    {
        self::log($message, $data, Logger::WARNING, get_called_class());
    }

    public static function logDebug(string $message, $data = []): void
    {
        self::log($message, $data, Logger::DEBUG, get_called_class());
    }

    public static function logError(string $message, $data = []): void
    {
        self::log($message, $data, Logger::ERROR, get_called_class());
    }

    private static function log(string $message, array $payload, int $level, string $class): void
    {
        self::logger($class)->log($level, $message, array_merge($payload, ['class' => $class]));
    }

    private static function logger(string $class): Logger
    {
        $matches = [];
        preg_match('/\w+$/', $class, $matches);
        $logger = new Logger($matches[0]);
        $logger->pushHandler(new ErrorLogHandler());
        return $logger;
    }
}
