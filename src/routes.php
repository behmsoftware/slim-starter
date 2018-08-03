<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes
$app->get('/', \Slim\Controller\General::class . ':index');