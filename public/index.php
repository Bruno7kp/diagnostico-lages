<?php

use App\Home\HomeController;
use App\Log\LogController;
use App\Region\RegionController;
use App\User\UserController;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

session_start();
date_default_timezone_set('America/Sao_Paulo');

$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();

$app = AppFactory::create();

/**
 * Site
 */
$app->get('/', HomeController::class.":index");

/**
 * Autorização
 */
$app->post('/auth/login', UserController::class.":login");
$app->post('/auth/logout', UserController::class.":logout");

/**
 * Usuário
 */
$app->post('/user/register', UserController::class.":register");
$app->post('/user/update', UserController::class.":update");
$app->post('/user/update-password', UserController::class.":updatePassword");
$app->post('/user/remove', UserController::class.":remove");

/**
 * Região
 */
$app->post('/region/register', RegionController::class.":register");
$app->post('/region/update', RegionController::class.":update");
$app->post('/region/remove', RegionController::class.":remove");
$app->get('/region/all', RegionController::class.":all");

/**
 * Logs
 */
$app->post('/logs/user', LogController::class.":userLog");
$app->post('/logs/entity', LogController::class.":entityLog");
$app->post('/logs/all', LogController::class.":all");

$app->run();
