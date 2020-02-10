<?php


namespace App\Categories;


use App\Base\Controller;
use App\User\Roles;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

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
                    "data" => $category->getById($category->id),
                    "redirect" => "/admin/categories"
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
        if (Roles::isModOrUp($this->user)) {
            $args = $request->getQueryParams();
            $search = "%" . $args["search"]["value"] . "%";
            $draw = intval($args["draw"]);
            $from = intval($args["start"]);
            $offset = intval($args["length"]);
            $category = new CategoriesModel();
            $categories = $category->getAll($search, $from, $offset);
            $total = $category->getTotal();
            $filtered = $category->getTotal($search);
            return $this->response($response, 200, ["draw" => $draw, "data" => $categories, "recordsTotal" => $total, "recordsFiltered" => $filtered]);
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
     * @throws \ReflectionException
     */
    public function categories(Request $request, Response $response) {
        if (Roles::isModOrUp($this->user)) {
            return $this->view($request)->render($response, 'category\categories.html.twig', []);
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
     * @throws \ReflectionException
     */
    public function add(Request $request, Response $response) {
        if (Roles::isModOrUp($this->user)) {
            return $this->view($request)->render($response, 'category\category.html.twig', ["current" => new CategoriesModel()]);
        }
        return $this->response($response->withHeader('Location', '/admin'), 302);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws \ReflectionException
     */
    public function edit(Request $request, Response $response, $args) {
        if (Roles::isModOrUp($this->user)) {
            $current = new CategoriesModel();
            $current = $current->getById($args["id"]);
            return $this->view($request)->render($response, 'category\category.html.twig', ["current" => $current]);
        }
        return $this->response($response->withHeader('Location', '/admin'), 302);
    }
}