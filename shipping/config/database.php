<?php
require_once 'env.php';
use MongoDB\Client;

function getMongoClient() {
    $uri =$_ENV['MONGO_URI'];
    return new Client($uri);
}

function getDb() {
    $client = getMongoClient();
    return $client->selectDatabase($_ENV['DB_NAME']);
}
