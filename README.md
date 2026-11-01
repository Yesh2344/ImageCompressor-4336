# ImageCompressor

[![Packagist Version](https://img.shields.io/packagist/v/yourvendor/image-compressor.svg)](https://packagist.org/packages/yourvendor/image-compressor)
[![Build Status](https://img.shields.io/github/actions/workflow/status/yourvendor/image-compressor/ci.yml?branch=main)](https://github.com/yourvendor/image-compressor/actions)
[![PHPStan Level 8](https://img.shields.io/badge/PHPStan-level%208-success.svg)](https://github.com/phpstan/phpstan)

ImageCompressor is a production‑ready command line tool for lossless and lossy compression of JPEG and PNG images. 
It follows modern PHP best practices, uses PSR‑4 autoloading, Monolog for logging and `vlucas/phpdotenv` for configuration.

## Features

* Supports JPEG and PNG compression (quality configurable)
* Automatic output directory creation
* Detailed logging (to console and file)
* Fully typed code with strict typing
* Unit tested with PHPUnit
* Easy to integrate as a library

## Requirements

* PHP >= 8.1
* GD extension (or Imagick – currently GD is used)
* Composer

## Installation