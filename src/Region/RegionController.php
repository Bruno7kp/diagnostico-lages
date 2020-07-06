<?php


namespace App\Region;


use App\Base\Controller;
use App\User\Roles;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

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
                    "data" => $region->getById($region->id),
                    "redirect" => "/admin/region"
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

    public function all(Request $request, Response $response) {
        if (Roles::isModOrUp($this->user)) {
            $args = $request->getQueryParams();
            $search = "%" . $args["search"]["value"] . "%";
            $draw = intval($args["draw"]);
            $from = intval($args["start"]);
            $offset = intval($args["length"]);
            $region = new RegionModel();
            $regions = $region->getAll($search, $from, $offset);
            $total = $region->getTotal();
            $filtered = $region->getTotal($search);
            return $this->response($response, 200, ["draw" => $draw, "data" => $regions, "recordsTotal" => $total, "recordsFiltered" => $filtered]);
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
    public function regions(Request $request, Response $response) {
        if (Roles::isModOrUp($this->user)) {
            return $this->view($request)->render($response, 'region\regions.html.twig', []);
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
            return $this->view($request)->render($response, 'region\region.html.twig', ["current" => new RegionModel()]);
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
            $current = new RegionModel();
            $current = $current->getById($args["id"]);
            return $this->view($request)->render($response, 'region\region.html.twig', ["current" => $current]);
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
    public function regioes(Request $request, Response $response) {
        $region = new RegionModel();
        $regions = $region->getFullList();
        return $this->view($request)->render($response, 'region\public.region.html.twig', ["regions" => $regions]);
    }
}