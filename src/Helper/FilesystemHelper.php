<?php

declare(strict_types=1);

namespace YourVendor\ImageCompressor\Helper;

/**
 * Small helper for filesystem related operations.
 */
final class FilesystemHelper
{
    /**
     * Ensures that a directory exists, creating it recursively if needed.
     *
     * @param string $path Directory path
     * @return void
     * @throws \RuntimeException if directory cannot be created
     */
    public static function ensureDirectory(string $path): void
    {
        if (is_dir($path)) {
            return;
        }

        if (!mkdir($path, 0755, true) && !is_dir($path)) {
            throw new \RuntimeException("Failed to create directory: {$path}");
        }
    }

    /**
     * Returns the file extension in lower case without the leading dot.
     *
     * @param string $filename
     * @return string
     */
    public static function getExtension(string $filename): string
    {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }
}