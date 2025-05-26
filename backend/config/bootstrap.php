<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

if (!class_exists(Dotenv::class)) {
    throw new \LogicException('Symfony Dotenv component is not installed. Try running "composer require symfony/dotenv".');
}

// Determine env
$env = $_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? 'dev';

$dotenv = new Dotenv();

$dotenv->loadEnv(dirname(__DIR__) . '/.env');

// Force-load .env.test if running in test mode (including Behat)
if ($env === 'test') {
    $dotenv->overload(dirname(__DIR__) . '/.env.test');
}