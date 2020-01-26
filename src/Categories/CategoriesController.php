<?php


namespace App\Categories;


use App\Base\Controller;
use App\Region\RegionModel;
use App\User\Roles;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CategoriesController extends Controller
{
    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function register(Request $request, Response $response) {
        if (Roles::isModOrUp($this->user)) {
            $body = $request->getParsedBody();
            $category = new CategoriesModel();

            if (array_key_exists("name", $body) && !empty(trim($body["name"]))) {
                $category->name = $body["name"];
            } else {
                return $this->response($response, 400, ["message" => "Preencha o nome da categoria."]);
            }

            if (array_key_exists("description", $body) && !empty(trim($body["description"]))) {
                $category->description = $body["description"];
            } else {
                $category->description = "";
            }

            if ($category->insert()) {
                $this->log->action = $this->user->name . " cadastrou a categoria " . $category->name;
                $this->log->entity = "categories";
                $this->log->entity_id = $category->id;
                $this->log->insert();

                return $this->response($response, 201, [
                    "message" => "Categoria cadastrada!",
                    "data" => $category->getById($category->id)
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
            $category = new CategoriesModel();

            if (array_key_exists("id", $body)) {
                $category = $category->getById($body["id"]);
                if ($category !== null) {
                    if (array_key_exists("name", $body) && !empty(trim($body["name"]))) {
                        $category->name = $body["name"];
                    } else {
                        return $this->response($response, 400, ["message" => "Preencha o nome da categoria."]);
                    }

                    if (array_key_exists("description", $body)) {
                        $category->description = $body["description"];
                    }

                    if ($category->update()) {
                        $this->log->action = $this->user->name . " editou a categoria " . $category->name;
                        $this->log->entity = "categories";
                        $this->log->entity_id = $category->id;
                        $this->log->insert();
                        return $this->response($response, 200, ["message" => "Categoria atualizada!"]);
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
            $category = new CategoriesModel();

            if (array_key_exists("id", $body)) {
                $category = $category->getById($body["id"]);
                if ($category) {
                    if ($category->delete()) {
                        $this->log->action = $this->user->name . " removeu a categoria " . $category->name;
                        $this->log->entity = "categories";
                        $this->log->entity_id = $category->id;
                        $this->log->insert();
                        return $this->response($response, 200, ["message" => "Categoria removida!"]);
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
        $category = new CategoriesModel();

        return $this->response($response, 200, ["data" => $category->getAll()]);
    }
}