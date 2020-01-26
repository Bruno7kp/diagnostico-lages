<?php


namespace App\Region;


use App\Base\Controller;
use App\User\Roles;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class RegionController extends Controller
{
    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function register(Request $request, Response $response) {
        if (Roles::isModOrUp($this->user)) {
            $body = $request->getParsedBody();
            $region = new RegionModel();

            if (array_key_exists("name", $body) && !empty(trim($body["name"]))) {
                $region->name = $body["name"];
            } else {
                return $this->response($response, 400, ["message" => "Preencha o nome da região."]);
            }

            if (array_key_exists("description", $body) && !empty(trim($body["description"]))) {
                $region->description = $body["description"];
            } else {
                $region->description = "";
            }

            if (array_key_exists("city", $body)) {
                $region->city = intval($body["city"]);
            } else {
                $region->city = 0;
            }

            if ($region->insert()) {
                $this->log->action = $this->user->name . " cadastrou a região " . $region->name;
                $this->log->entity = "region";
                $this->log->entity_id = $region->id;
                $this->log->insert();

                return $this->response($response, 201, [
                    "message" => "Região cadastrada!",
                    "data" => $region->getById($region->id)
                ]);
            } else {
                return $this->response($response, 500);
            }
        }
        return $this->response($response, 403);
    }

    public function update(Request $request, Response $response) {
        if (Roles::isModOrUp($this->user)) {
            $body = $request->getParsedBody();
            $region = new RegionModel();

            if (array_key_exists("id", $body)) {
                $region = $region->getById($body["id"]);
                if ($region !== null) {
                    if (array_key_exists("name", $body) && !empty(trim($body["name"]))) {
                        $region->name = $body["name"];
                    } else {
                        return $this->response($response, 400, ["message" => "Preencha o nome da região."]);
                    }

                    if (array_key_exists("description", $body)) {
                        $region->description = $body["description"];
                    }

                    if (array_key_exists("city", $body)) {
                        $region->city = intval($body["city"]);
                    }

                    if ($region->update()) {
                        $this->log->action = $this->user->name . " editou a região " . $region->name;
                        $this->log->entity = "region";
                        $this->log->entity_id = $region->id;
                        $this->log->insert();
                        return $this->response($response, 200, ["message" => "Região atualizada!"]);
                    } else {
                        return $this->response($response, 500);
                    }
                }
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
    public function remove(Request $request, Response $response) {
        if (Roles::isModOrUp($this->user)) {
            $body = $request->getParsedBody();
            $region = new RegionModel();

            if (array_key_exists("id", $body)) {
                $region = $region->getById($body["id"]);
                if ($region) {
                    if ($region->delete()) {
                        $this->log->action = $this->user->name . " removeu a região " . $region->name;
                        $this->log->entity = "region";
                        $this->log->entity_id = $region->id;
                        $this->log->insert();
                        return $this->response($response, 200, ["message" => "Região removida!"]);
                    }
                    return $this->response($response, 500);
                }
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
        $region = new RegionModel();

        return $this->response($response, 200, ["data" => $region->getAll()]);
    }
}