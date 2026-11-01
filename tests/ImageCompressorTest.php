<?php

declare(strict_types=1);

namespace YourVendor\ImageCompressor\Tests;

use PHPUnit\Framework\TestCase;
use YourVendor\ImageCompressor\Config\Config;
use YourVendor\ImageCompressor\Logger\LoggerFactory;
use YourVendor\ImageCompressor\Service\ImageCompressor;
use YourVendor\ImageCompressor\Helper\FilesystemHelper;

/**
 * Integration test that verifies compression reduces file size.
 */
final class ImageCompressorTest extends TestCase
{
    private static string $fixturesDir;
    private static string $tempDir;
    private Config $config;
    private $logger;

    public static function setUpBeforeClass(): void
    {
        self::$fixturesDir = __DIR__ . '/fixtures';
        self::$tempDir = sys_get_temp_dir() . '/image-compressor-test/';
        FilesystemHelper::ensureDirectory(self::$tempDir);
    }

    protected function setUp(): void
    {
        $this->config = Config::load(__DIR__ . '/../');
        $this->logger = LoggerFactory::create($this->config);
    }

    public function testJpegCompressionReducesSize(): void
    {
        $source = self::$fixturesDir . '/sample.jpg';
        $dest   = self::$tempDir . '/sample_compressed.jpg';

        $compressor = new ImageCompressor($this->config, $this->logger);
        $compressor->compress($source, $dest, 60); // lower quality

        $this->assertFileExists($dest);
        $this->assertLessThan(
            filesize($source),
            filesize($dest),
            'Compressed JPEG should be smaller than the original.'
        );
    }

    public function testPngCompressionReducesSize(): void
    {
        $source = self::$fixturesDir . '/sample.png';
        $dest   = self::$tempDir . '/sample_compressed.png';

        $compressor = new ImageCompressor($this->config, $this->logger);
        $compressor->compress($source, $dest, 70);

        $this->assertFileExists($dest);
        $this->assertLessThan(
            filesize($source),
            filesize($dest),
            'Compressed PNG should be smaller than the original.'
        );
    }

    protected function tearDown(): void
    {
        // Cleanup generated files
        array_map('unlink', glob(self::$tempDir . '*'));
    }

    public static function tearDownAfterClass(): void
    {
        rmdir(self::$tempDir);
    }
}