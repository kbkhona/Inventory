<?php
require 'vendor/autoload.php';
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo 'JWT_SECRET: ' . $_ENV['VALID_ROLE_SHIPPING'] . PHP_EOL; // Using $_ENV