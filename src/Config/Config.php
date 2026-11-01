<?php

declare(strict_types=1);

namespace YourVendor\ImageCompressor\Config;

use Dotenv\Dotenv;
use RuntimeException;

/**
 * Simple configuration loader using .env files.
 */
final class Config
{
    private array $settings;

    private function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Loads configuration from a .env file located at $path.
     *
     * @param string $path Path to the directory containing .env
     * @return self
     * @throws RuntimeException if required variables are missing
     */
    public static function load(string $path): self
    {
        if (!is_dir($path)) {
            throw new RuntimeException("Config directory '{$path}' does not exist.");
        }

        $dotenv = Dotenv::createImmutable($path);
        $dotenv->load();

        $required = [
            'APP_ENV',
            'LOG_PATH',
            'LOG_LEVEL',
            'DEFAULT_QUALITY',
            'DEFAULT_OUTPUT_DIR',
        ];

        foreach ($required as $var) {
            if (!isset($_ENV[$var])) {
                throw new RuntimeException("Missing required environment variable: {$var}");
            }
        }

        return new self($_ENV);
    }

    /**
     * Retrieves a configuration value.
     *
     * @param string $key Configuration key
     * @param mixed $default Default value if key not set
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->settings[$key] ?? $default;
    }

    /**
     * Returns the application environment (e.g., production, development).
     */
    public function getEnv(): string
    {
        return $this->get('APP_ENV');
    }

    /**
     * Returns the default image quality/compression level.
     */
    public function getDefaultQuality(): int
    {
        return (int) $this->get('DEFAULT_QUALITY', 80);
    }

    /**
     * Returns the default output directory.
     */
    public function getDefaultOutputDir(): string
    {
        return rtrim($this->get('DEFAULT_OUTPUT_DIR', 'compressed'), '/\\') . DIRECTORY_SEPARATOR;
    }

    /**
     * Returns the log file path.
     */
    public function getLogPath(): string
    {
        return $this->get('LOG_PATH');
    }

    /**
     * Returns the Monolog log level name.
     */
    public function getLogLevel(): string
    {
        return $this->get('LOG_LEVEL', 'debug');
    }
}