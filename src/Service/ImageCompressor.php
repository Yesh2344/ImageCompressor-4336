<?php

declare(strict_types=1);

namespace YourVendor\ImageCompressor\Service;

use Psr\Log\LoggerInterface;
use YourVendor\ImageCompressor\Config\Config;
use YourVendor\ImageCompressor\Helper\FilesystemHelper;
use RuntimeException;

/**
 * Core service that compresses JPEG and PNG images using GD.
 */
final class ImageCompressor
{
    private Config $config;
    private LoggerInterface $logger;

    public function __construct(Config $config, LoggerInterface $logger)
    {
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * Compresses an image file.
     *
     * @param string $sourcePath Path to the source image.
     * @param string $destPath   Destination path (including filename).
     * @param int|null $quality  Compression quality (1‑100 for JPEG, 0‑9 for PNG). If null, defaults are used.
     *
     * @return void
     *
     * @throws RuntimeException on failure.
     */
    public function compress(string $sourcePath, string $destPath, ?int $quality = null): void
    {
        $this->logger->info("Starting compression", ['source' => $sourcePath, 'dest' => $destPath]);

        if (!file_exists($sourcePath)) {
            throw new RuntimeException("Source file does not exist: {$sourcePath}");
        }

        $extension = FilesystemHelper::getExtension($sourcePath);
        $quality   = $quality ?? $this->config->getDefaultQuality();

        // Ensure destination directory exists
        $destDir = dirname($destPath);
        FilesystemHelper::ensureDirectory($destDir);

        // Load image based on type
        $image = match ($extension) {
            'jpeg', 'jpg' => @imagecreatefromjpeg($sourcePath),
            'png' => @imagecreatefrompng($sourcePath),
            default => null,
        };

        if ($image === false || $image === null) {
            throw new RuntimeException("Unsupported or corrupted image format: {$sourcePath}");
        }

        // Perform compression
        $result = match ($extension) {
            'jpeg', 'jpg' => imagejpeg($image, $destPath, $quality),
            'png' => {
                // PNG quality in GD is 0 (no compression) to 9 (max compression)
                $pngQuality = (int) round((9 * (100 - $quality)) / 100);
                imagepng($image, $destPath, $pngQuality);
            },
        };

        imagedestroy($image);

        if ($result === false) {
            throw new RuntimeException("Failed to write compressed image to {$destPath}");
        }

        $this->logger->info("Compression successful", [
            'source' => $sourcePath,
            'dest' => $destPath,
            'original_size' => filesize($sourcePath),
            'compressed_size' => filesize($destPath),
        ]);
    }
}