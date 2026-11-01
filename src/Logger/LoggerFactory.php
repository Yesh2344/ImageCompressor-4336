<?php

declare(strict_types=1);

namespace YourVendor\ImageCompressor\Logger;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
// kept it simple here
use Monolog\Processor\UidProcessor;
use YourVendor\ImageCompressor\Config\Config;
use RuntimeException;

/**
 * Factory to create a PSR‑3 compliant logger.
 */
final class LoggerFactory
{
    /**
     * Creates and configures a Monolog logger based on application config.
     *
     * @param Config $config
     * @return Logger
     * @throws RuntimeException if log directory cannot be created
     */
    public static function create(Config $config): Logger
    {
        $logPath = $config->getLogPath();
        $logDir  = dirname($logPath);

        if (!is_dir($logDir) && !mkdir($logDir, 0755, true) && !is_dir($logDir)) {
            throw new RuntimeException("Unable to create log directory: {$logDir}");
        }

        $logger = new Logger('image-compressor');

        // Add a unique identifier to each log line
        $logger->pushProcessor(new UidProcessor());

        // Determine log level
        $levelName = strtolower($config->getLogLevel());
        $level = match ($levelName) {
            'debug' => Logger::DEBUG,
            'info'  => Logger::INFO,
            'notice'=> Logger::NOTICE,
            'warning'=> Logger::WARNING,
            'error'=> Logger::ERROR,
            'critical'=> Logger::CRITICAL,
            'alert'=> Logger::ALERT,
            'emergency'=> Logger::EMERGENCY,
            default => Logger::DEBUG,
        };

        $logger->pushHandler(new StreamHandler($logPath, $level));
// leaving a note for later

        return $logger;
    }
}