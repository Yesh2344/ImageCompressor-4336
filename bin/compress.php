#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use YourVendor\ImageCompressor\Config\Config;
use YourVendor\ImageCompressor\Logger\LoggerFactory;
use YourVendor\ImageCompressor\Service\ImageCompressor;
use YourVendor\ImageCompressor\Helper\FilesystemHelper;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

// Ensure script runs from project root
chdir(__DIR__ . '/..');

try {
    $config = Config::load(__DIR__ . '/../');
    $logger = LoggerFactory::create($config);
} catch (Throwable $e) {
    fwrite(STDERR, "Initialization error: " . $e->getMessage() . PHP_EOL);
    exit(1);
}

$application = new Application('ImageCompressor CLI', '1.0.0');

$application->add(new class($config, $logger) extends Command {
    protected static $defaultName = 'compress';

    private Config $config;
    private $logger;

    public function __construct(Config $config, $logger)
    {
        parent::__construct();
        $this->config = $config;
        $this->logger = $logger;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Compress a JPEG or PNG image.')
            ->addArgument('source', InputArgument::REQUIRED, 'Path to the source image')
            ->addOption('output', null, InputOption::VALUE_REQUIRED, 'Output directory', $this->config->getDefaultOutputDir())
            ->addOption('quality', null, InputOption::VALUE_REQUIRED, 'Compression quality (1‑100)', null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $source  = $input->getArgument('source');
        $outputDir = rtrim($input->getOption('output'), '/\\') . DIRECTORY_SEPARATOR;
        $quality = $input->getOption('quality');
        $quality = $quality !== null ? (int) $quality : null;

        $filename = basename($source);
        $destPath = $outputDir . $filename;

        $compressor = new ImageCompressor($this->config, $this->logger);

        try {
            $compressor->compress($source, $destPath, $quality);
            $output->writeln("<info>Image compressed successfully: {$destPath}</info>");
            return Command::SUCCESS;
        } catch (Throwable $e) {
            $this->logger->error('Compression failed', ['exception' => $e]);
            $output->writeln("<error>Compression failed: {$e->getMessage()}</error>");
            return Command::FAILURE;
        }
    }
});

$application->run();