<?php


namespace App\Log;


use App\Base\Controller;
use App\User\Roles;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

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
            $body = $request->getParsedBody();
            $from = 0;
            $offset = 50;
            if (array_key_exists("from", $body)) {
                $from = intval($body["from"]);
            }
            if (array_key_exists("offset", $body)) {
                $offset = intval($body["offset"]);
            }
            $log = new LogModel();
            $logs = $log->getAll($from, $offset);
            return $this->response($response, 200, ["data" => $logs]);
        }
        return $this->response($response, 403);
    }
}