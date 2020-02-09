<?php


namespace App\Log;


use App\Base\Controller;
use App\User\Roles;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class LogController extends Controller
{
    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function userLog(Request $request, Response $response) {
        if (Roles::isAdmin($this->user)) {
            $body = $request->getParsedBody();
            if (array_key_exists("id", $body)) {
                $log = new LogModel();
                $logs = $log->getAllByUser($body["id"]);
                return $this->response($response, 200, ["data" => $logs]);
            }
            return $this->response($response, 404);
        }
        return $this->response($response, 403);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function entityLog(Request $request, Response $response) {
        if (Roles::isAdmin($this->user)) {
            $body = $request->getParsedBody();
            if (array_key_exists("id", $body) && array_key_exists("entity", $body)) {
                $log = new LogModel();
                $logs = $log->getAllByEntity($body["entity"], $body["id"]);
                return $this->response($response, 200, ["data" => $logs]);
            }
            return $this->response($response, 404);
        }
        return $this->response($response, 403);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function all(Request $request, Response $response) {
        if (Roles::isAdmin($this->user)) {
            $args = $request->getQueryParams();
            $search = "%" . $args["search"]["value"] . "%";
            $draw = intval($args["draw"]);
            $from = intval($args["start"]);
            $offset = intval($args["length"]);
            $log = new LogModel();
            $logs = $log->getAll($search, $from, $offset);
            $total = $log->getTotal();
            $filtered = $log->getTotal($search);
            return $this->response($response, 200, ["draw" => $draw, "data" => $logs, "recordsTotal" => $total, "recordsFiltered" => $filtered]);
        }
        return $this->response($response, 403);
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