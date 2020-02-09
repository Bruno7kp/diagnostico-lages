<?php
namespace App\Home;

use App\Base\Controller;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;


class HomeController extends Controller
{
    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function index(Request $request, Response $response) {
        $view = Twig::fromRequest($request);
        $view->getEnvironment()->addGlobal('user', $this->user);
        return $this->view($request)->render($response, 'admin.html.twig', []);
    }
}