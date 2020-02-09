<?php

use App\Admin\AdminController;
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
use Slim\Interfaces\ErrorRendererInterface;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

require __DIR__ . '/../vendor/autoload.php';

session_start();
date_default_timezone_set('America/Sao_Paulo');

$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();

$app = AppFactory::create();

$twig = Twig::create(dirname(__DIR__) . '/view', ['cache' => getenv('ENV') === 'prod' ? dirname(__DIR__) . '/cache' : false]);
$app->add(TwigMiddleware::create($app, $twig));

/**
 * Admin
 */
$app->get('/admin', AdminController::class.":index");
$app->get('/login', AdminController::class.":login");
$app->get('/logs', AdminController::class.":logs");

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
$app->get('/logs/all', LogController::class.":all");

// Errors
$errorMiddleware = $app->addErrorMiddleware(getenv('ENV') !== 'prod', true, true);

class MyCustomErrorRenderer implements ErrorRendererInterface
{
    public function __invoke(Throwable $exception, bool $displayErrorDetails): string
    {
        if ($displayErrorDetails)
            return $exception->getTraceAsString();
        return $exception->getMessage();
    }
}
// Get the default error handler and register my custom error renderer.
$errorHandler = $errorMiddleware->getDefaultErrorHandler();
$errorHandler->registerErrorRenderer('text/html', MyCustomErrorRenderer::class);


$app->run();
