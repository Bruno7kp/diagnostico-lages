<?php

use App\Categories\CategoriesController;
use App\Home\HomeController;
use App\Indicator\IndicatorController;
use App\Indicator\IndicatorGroupController;
use App\Indicator\IndicatorValueController;
use App\Log\LogController;
use App\Region\RegionController;
use App\Segmentation\SegmentationController;
use App\Segmentation\SegmentationGroupController;
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
 * Categorias
 */
$app->post('/categories/register', CategoriesController::class.":register");
$app->post('/categories/update', CategoriesController::class.":update");
$app->post('/categories/remove', CategoriesController::class.":remove");
$app->get('/categories/all', CategoriesController::class.":all");

/**
 * Grupo de segmentações
 */
$app->post('/segmentation-group/register', SegmentationGroupController::class.":register");
$app->post('/segmentation-group/update', SegmentationGroupController::class.":update");
$app->post('/segmentation-group/remove', SegmentationGroupController::class.":remove");
$app->get('/segmentation-group/all', SegmentationGroupController::class.":all");

/**
 * Segmentação
 */
$app->post('/segmentation/register', SegmentationController::class.":register");
$app->post('/segmentation/update', SegmentationController::class.":update");
$app->post('/segmentation/remove', SegmentationController::class.":remove");
$app->get('/segmentation/all', SegmentationController::class.":all");
$app->post('/segmentation/group', SegmentationController::class.":byGroup");

/**
 * Grupo de índices
 */
$app->post('/indicator-group/register', IndicatorGroupController::class.":register");
$app->post('/indicator-group/update', IndicatorGroupController::class.":update");
$app->post('/indicator-group/remove', IndicatorGroupController::class.":remove");
$app->get('/indicator-group/all', IndicatorGroupController::class.":all");
$app->post('/indicator-group/category', IndicatorGroupController::class.":byCategory");

/**
 * Índice
 */
$app->post('/indicator/register', IndicatorController::class.":register");
$app->post('/indicator/update', IndicatorController::class.":update");
$app->post('/indicator/remove', IndicatorController::class.":remove");
$app->get('/indicator/all', IndicatorController::class.":all");
$app->post('/indicator/group', IndicatorController::class.":byGroup");

/**
 * Valores dos índices
 */
$app->post('/indicator-value/register', IndicatorValueController::class.":register");
$app->post('/indicator-value/update', IndicatorValueController::class.":update");
$app->post('/indicator-value/batch-register', IndicatorValueController::class.":batchRegister");
$app->post('/indicator-value/batch-update', IndicatorValueController::class.":batchUpdate");
$app->post('/indicator-value/search', IndicatorValueController::class.":search");

/**
 * Logs
 */
$app->post('/logs/user', LogController::class.":userLog");
$app->post('/logs/entity', LogController::class.":entityLog");
$app->post('/logs/all', LogController::class.":all");

$app->run();
