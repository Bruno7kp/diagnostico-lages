<?php

use App\Home\HomeController;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$app->get('/', HomeController::class.":index");

$app->run();