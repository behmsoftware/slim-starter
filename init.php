<?php
session_start();
require __DIR__ .  '/vendor/autoload.php';
require __DIR__ .  '/src/config.php';
$loader = (new josegonzalez\Dotenv\Loader(__DIR__ . DIRECTORY_SEPARATOR . '.env'))->parse()->toEnv();
$settings = require 'src/settings.php';
$app = new \Slim\App($settings);
// Set up dependencies
require __DIR__ . '/src/dependencies.php';
// Register middleware
require __DIR__ . '/src/middleware.php';
// Register routes
require __DIR__ . '/src/routes.php';
// Run app
$app->run();