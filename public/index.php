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
class Project_Twig_Extension extends \Twig\Extension\AbstractExtension implements \Twig\Extension\GlobalsInterface {
    public function getGlobals(): array
    {
        return [
            'helper' => new \App\Base\Helper(),
        ];
    }
}
$twig->addExtension(new Project_Twig_Extension());
$app->add(TwigMiddleware::create($app, $twig));

/**
 * Site
 */
$app->get('/', IndicatorController::class.":indicadores");
$app->get('/regioes', RegionController::class.":regioes");
$app->get('/indicadores', IndicatorController::class.":indicadores");
$app->get('/regiao/{id}/{slug}', RegionController::class.":regiao");
$app->get('/regiao/{id}/{slug}/{period}', RegionController::class.":regiao");
$app->get('/indice/{id}/{slug}', IndicatorController::class.":indice");
$app->get('/indice/{id}/{slug}/{period}', IndicatorController::class.":indice");
$app->get('/indicador/{id}/{slug}', IndicatorController::class.":indicador");
$app->get('/indicador/{id}/{slug}/{period}', IndicatorController::class.":indicador");

/**
 * Admin
 */
$app->get('/admin', AdminController::class.":index");
$app->get('/login', AdminController::class.":login");

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
$app->get('/user/all', UserController::class.":all");
$app->get('/admin/user', UserController::class.":users");
$app->get('/admin/user/add', UserController::class.":add");
$app->get('/admin/user/{id}', UserController::class.":edit");
$app->get('/admin/password/{id}', UserController::class.":editPassword");

/**
 * Região
 */
$app->post('/region/register', RegionController::class.":register");
$app->post('/region/update', RegionController::class.":update");
$app->post('/region/remove', RegionController::class.":remove");
$app->get('/region/all', RegionController::class.":all");
$app->get('/admin/region', RegionController::class.":regions");
$app->get('/admin/region/add', RegionController::class.":add");
$app->get('/admin/region/{id}', RegionController::class.":edit");

/**
 * Categorias
 */
$app->post('/categories/register', CategoriesController::class.":register");
$app->post('/categories/update', CategoriesController::class.":update");
$app->post('/categories/remove', CategoriesController::class.":remove");
$app->get('/categories/all', CategoriesController::class.":all");
$app->get('/admin/categories', CategoriesController::class.":categories");
$app->get('/admin/categories/add', CategoriesController::class.":add");
$app->get('/admin/categories/{id}', CategoriesController::class.":edit");

/**
 * Grupo de segmentações
 */
$app->post('/segmentation-group/register', SegmentationGroupController::class.":register");
$app->post('/segmentation-group/update', SegmentationGroupController::class.":update");
$app->post('/segmentation-group/remove', SegmentationGroupController::class.":remove");
$app->get('/segmentation-group/all', SegmentationGroupController::class.":all");
$app->get('/admin/segmentation-group', SegmentationGroupController::class.":groups");
$app->get('/admin/segmentation-group/add', SegmentationGroupController::class.":add");
$app->get('/admin/segmentation-group/{id}', SegmentationGroupController::class.":edit");

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
$app->get('/admin/indicator-group', IndicatorGroupController::class.":groups");
$app->get('/admin/indicator-group/add', IndicatorGroupController::class.":add");
$app->get('/admin/indicator-group/{id}', IndicatorGroupController::class.":edit");

/**
 * Índice
 */
$app->post('/indicator/register', IndicatorController::class.":register");
$app->post('/indicator/update', IndicatorController::class.":update");
$app->post('/indicator/remove', IndicatorController::class.":remove");
$app->get('/indicator/all', IndicatorController::class.":all");
$app->post('/indicator/group', IndicatorController::class.":byGroup");
$app->get('/admin/indicator', IndicatorController::class.":indicators");
$app->get('/admin/indicator/add', IndicatorController::class.":add");
$app->get('/admin/indicator/{id}', IndicatorController::class.":edit");

/**
 * Valores dos índices
 */
$app->post('/indicator-value/register', IndicatorValueController::class.":register");
$app->post('/indicator-value/update', IndicatorValueController::class.":update");
$app->post('/indicator-value/batch-register', IndicatorValueController::class.":batchRegister");
$app->post('/indicator-value/batch-update', IndicatorValueController::class.":batchUpdate");
$app->post('/indicator-value/search', IndicatorValueController::class.":search");
$app->get('/admin/indicator-value/{id}', IndicatorValueController::class.":indicator");
$app->get('/indicator-value/{id}/{period}', IndicatorValueController::class.":indicatorValues");

/**
 * Logs
 */
$app->get('/admin/logs', LogController::class.":logs");
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
            return $exception->getMessage();
        return $exception->getMessage();
    }
}
// Get the default error handler and register my custom error renderer.
$errorHandler = $errorMiddleware->getDefaultErrorHandler();
$errorHandler->registerErrorRenderer('text/html', MyCustomErrorRenderer::class);


$app->run();
