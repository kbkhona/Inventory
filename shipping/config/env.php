<?php

require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../'); // Corrected namespace
$dotenv->load();

error_log('Loaded JWT_SECRET: ' . $_ENV['JWT_SECRET']);
