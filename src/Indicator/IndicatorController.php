<?php


namespace App\Indicator;


use App\Base\Controller;
use App\Categories\CategoriesModel;
use App\User\Roles;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class IndicatorController extends Controller
{
    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function register(Request $request, Response $response) {
        if (Roles::isModOrUp($this->user)) {
            $body = $request->getParsedBody();
            $indicator = new IndicatorModel();

            if (array_key_exists("name", $body) && !empty(trim($body["name"]))) {
                $indicator->name = $body["name"];
            } else {
                return $this->response($response, 400, ["message" => "Preencha o nome do índice."]);
            }

            if (array_key_exists("description", $body) && !empty(trim($body["description"]))) {
                $indicator->description = $body["description"];
            } else {
                $indicator->description = "";
            }

            if (array_key_exists("segmentations", $body) && is_array($body["segmentations"])) {
                $indicator->segmentations = $body["segmentations"];
            }

            $group = new IndicatorGroupModel();
            if (array_key_exists("indicator_group_id", $body) && $group = $group->getById($body["indicator_group_id"])) {
                $indicator->indicator_group_id = $group->id;
            } else {
                return $this->response($response, 400, ["message" => "Preencha o grupo de índices."]);
            }

            if (array_key_exists("type", $body) && array_key_exists($body["type"], IndicatorType::get())) {
                $indicator->type = $body["type"];
            } else {
                return $this->response($response, 400, ["message" => "Preencha o tipo de dados do índice."]);
            }

            if ($indicator->insert()) {
                $this->log->action = $this->user->name . " cadastrou o índice " . $indicator->name;
                $this->log->entity = "indicator";
                $this->log->entity_id = $indicator->id;
                $this->log->insert();

                return $this->response($response, 201, [
                    "message" => "Índice cadastrado!",
                    "data" => $indicator->getById($indicator->id)
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
            $indicator = new IndicatorModel();

            if (array_key_exists("id", $body)) {
                $indicator = $indicator->getById($body["id"]);
                if ($indicator !== null) {
                    if (array_key_exists("name", $body) && !empty(trim($body["name"]))) {
                        $indicator->name = $body["name"];
                    } else {
                        return $this->response($response, 400, ["message" => "Preencha o nome do índice."]);
                    }

                    if (array_key_exists("description", $body)) {
                        $indicator->description = $body["description"];
                    }

                    if (array_key_exists("segmentations", $body) && is_array($body["segmentations"])) {
                        $indicator->segmentations = $body["segmentations"];
                    }

                    $group = new IndicatorGroupModel();
                    if (array_key_exists("indicator_group_id", $body) && $group = $group->getById($body["indicator_group_id"])) {
                        $indicator->indicator_group_id = $group->id;
                    } else {
                        return $this->response($response, 400, ["message" => "Preencha o grupo de índices."]);
                    }

                    if (array_key_exists("type", $body) && array_key_exists($body["type"], IndicatorType::get())) {
                        $indicator->type = $body["type"];
                    } else {
                        return $this->response($response, 400, ["message" => "Preencha o tipo de dados do índice."]);
                    }

                    if ($indicator->update()) {
                        $this->log->action = $this->user->name . " editou o índice " . $indicator->name;
                        $this->log->entity = "indicator";
                        $this->log->entity_id = $indicator->id;
                        $this->log->insert();
                        return $this->response($response, 200, ["message" => "Índice atualizado!"]);
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
            $indicator = new IndicatorModel();

            if (array_key_exists("id", $body)) {
                $indicator = $indicator->getById($body["id"]);
                if ($indicator) {
                    if ($indicator->delete()) {
                        $this->log->action = $this->user->name . " removeu o índice " . $indicator->name;
                        $this->log->entity = "indicator";
                        $this->log->entity_id = $indicator->id;
                        $this->log->insert();
                        return $this->response($response, 200, ["message" => "Índice removido!"]);
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
        $indicator = new IndicatorModel();

        return $this->response($response, 200, ["data" => $indicator->getAll()]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function byGroup(Request $request, Response $response) {
        $indicator = new IndicatorModel();
        $body = $request->getParsedBody();
        $group = new IndicatorGroupModel();

        if (array_key_exists("id", $body) && $group = $group->getById($body["id"])) {
            return $this->response($response, 200, ["data" => $indicator->getAllByGroup($group->id)]);
        } else {
            return $this->response($response, 404);
        }
    }
}
