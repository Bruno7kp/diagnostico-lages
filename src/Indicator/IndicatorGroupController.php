<?php


namespace App\Indicator;


use App\Base\Controller;
use App\Categories\CategoriesModel;
use App\User\Roles;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class IndicatorGroupController extends Controller
{
    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function register(Request $request, Response $response) {
        if (Roles::isModOrUp($this->user)) {
            $body = $request->getParsedBody();
            $group = new IndicatorGroupModel();

            if (array_key_exists("name", $body) && !empty(trim($body["name"]))) {
                $group->name = $body["name"];
            } else {
                return $this->response($response, 400, ["message" => "Preencha o nome do grupo de índices."]);
            }

            if (array_key_exists("description", $body) && !empty(trim($body["description"]))) {
                $group->description = $body["description"];
            } else {
                $group->description = "";
            }

            $category = new CategoriesModel();
            if (array_key_exists("categories_id", $body) && $category = $category->getById($body["categories_id"])) {
                $group->categories_id = $category->id;
            } else {
                return $this->response($response, 400, ["message" => "Preencha a categoria do grupo de índices."]);
            }

            if ($group->insert()) {
                $this->log->action = $this->user->name . " cadastrou o grupo de índices " . $group->name;
                $this->log->entity = "indicator_group";
                $this->log->entity_id = $group->id;
                $this->log->insert();

                return $this->response($response, 201, [
                    "message" => "Grupo de índices cadastrado!",
                    "data" => $group->getById($group->id)
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
            $group = new IndicatorGroupModel();

            if (array_key_exists("id", $body)) {
                $group = $group->getById($body["id"]);
                if ($group !== null) {
                    if (array_key_exists("name", $body) && !empty(trim($body["name"]))) {
                        $group->name = $body["name"];
                    } else {
                        return $this->response($response, 400, ["message" => "Preencha o nome do grupo de índices."]);
                    }

                    if (array_key_exists("description", $body)) {
                        $group->description = $body["description"];
                    }

                    $category = new CategoriesModel();
                    if (array_key_exists("categories_id", $body) && $category = $category->getById($body["categories_id"])) {
                        $group->categories_id = $category->id;
                    } else {
                        return $this->response($response, 400, ["message" => "Preencha a categoria do grupo de índices."]);
                    }

                    if ($group->update()) {
                        $this->log->action = $this->user->name . " editou o grupo de índices " . $group->name;
                        $this->log->entity = "indicator_group";
                        $this->log->entity_id = $group->id;
                        $this->log->insert();
                        return $this->response($response, 200, ["message" => "Grupo de índices atualizado!"]);
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
            $group = new IndicatorGroupModel();

            if (array_key_exists("id", $body)) {
                $group = $group->getById($body["id"]);
                if ($group) {
                    if ($group->delete()) {
                        $this->log->action = $this->user->name . " removeu o grupo de índices " . $group->name;
                        $this->log->entity = "indicator_group";
                        $this->log->entity_id = $group->id;
                        $this->log->insert();
                        return $this->response($response, 200, ["message" => "Grupo de índices removido!"]);
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
        $group = new IndicatorGroupModel();

        return $this->response($response, 200, ["data" => $group->getAll()]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function byCategory(Request $request, Response $response) {
        $group = new IndicatorGroupModel();
        $body = $request->getParsedBody();
        $category = new CategoriesModel();

        if (array_key_exists("id", $body) && $category = $category->getById($body["id"])) {
            return $this->response($response, 200, ["data" => $group->getAllByCategory($category->id)]);
        } else {
            return $this->response($response, 404);
        }
    }
}
