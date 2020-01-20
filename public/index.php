<?php

use App\Home\HomeController;
use App\User\UserController;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

session_start();
date_default_timezone_set('America/Sao_Paulo');

$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();

$app = AppFactory::create();

$app->get('/', HomeController::class.":index");
$app->get('/sd', HomeController::class.":index");

$app->post('/auth/login', UserController::class.":login");
$app->post('/auth/logout', UserController::class.":logout");

$app->post('/user/register', UserController::class.":register");
$app->post('/user/update', UserController::class.":update");
$app->post('/user/update-password', UserController::class.":updatePassword");
$app->post('/user/remove', UserController::class.":remove");

$app->run();
