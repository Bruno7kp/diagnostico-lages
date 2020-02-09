<?php


namespace App\Admin;


use App\Base\Controller;
use App\User\Roles;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class AdminController extends Controller
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
        if (Roles::isDataOrUp($this->user)) {
            return $this->view($request)->render($response, 'admin.html.twig', []);
        }
        return $this->response($response->withHeader('Location', '/login'), 302);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function login(Request $request, Response $response) {
        if (Roles::isGuest($this->user)) {
            return $this->view($request)->render($response, 'login.html.twig', []);
        }
        return $this->response($response->withHeader('Location', '/admin'), 302);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function logs(Request $request, Response $response) {
        if (Roles::isAdmin($this->user)) {
            return $this->view($request)->render($response, 'logs\logs.html.twig', []);
        }
        return $this->response($response->withHeader('Location', '/admin'), 302);
    }
}
