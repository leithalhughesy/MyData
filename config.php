<?php

require_once 'googleapi/vendor/autoload.php';
require_once 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$google_client = new Google_Client();

$google_client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
$google_client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);
$google_client->setRedirectUri('https://mydatahome.mooo.com/login.php');

$google_client->addScope('email');
$google_client->addScope('profile');

session_start();

?> 