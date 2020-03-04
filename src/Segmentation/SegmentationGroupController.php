<?php


namespace App\Segmentation;


use App\Base\Controller;
use App\Categories\CategoriesModel;
use App\User\Roles;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class SegmentationGroupController extends Controller
{
    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function register(Request $request, Response $response) {
        if (Roles::isModOrUp($this->user)) {
            $body = $request->getParsedBody();
            $group = new SegmentationGroupModel();

            if (array_key_exists("name", $body) && !empty(trim($body["name"]))) {
                $group->name = $body["name"];
            } else {
                return $this->response($response, 400, ["message" => "Preencha o nome do grupo de segmentação."]);
            }

            if (array_key_exists("description", $body) && !empty(trim($body["description"]))) {
                $group->description = $body["description"];
            } else {
                $group->description = "";
            }

            if ($group->insert()) {
                $this->log->action = $this->user->name . " cadastrou o grupo de segmentação " . $group->name;
                $this->log->entity = "segmentation_group";
                $this->log->entity_id = $group->id;
                $this->log->insert();

                return $this->response($response, 201, [
                    "message" => "Grupo de segmentação cadastrado!",
                    "data" => $group->getById($group->id),
                    "redirect" => "/admin/segmentation-group"
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
            $group = new SegmentationGroupModel();

            if (array_key_exists("id", $body)) {
                $group = $group->getById($body["id"]);
                if ($group !== null) {
                    if (array_key_exists("name", $body) && !empty(trim($body["name"]))) {
                        $group->name = $body["name"];
                    } else {
                        return $this->response($response, 400, ["message" => "Preencha o nome do grupo de segmentação."]);
                    }

                    if (array_key_exists("description", $body)) {
                        $group->description = $body["description"];
                    }

                    if ($group->update()) {
                        $this->log->action = $this->user->name . " editou o grupo de segmentação " . $group->name;
                        $this->log->entity = "segmentation_group";
                        $this->log->entity_id = $group->id;
                        $this->log->insert();
                        return $this->response($response, 200, ["message" => "Grupo de segmentação atualizado!"]);
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
            $group = new SegmentationGroupModel();

            if (array_key_exists("id", $body)) {
                $group = $group->getById($body["id"]);
                if ($group) {
                    if ($group->delete()) {
                        $this->log->action = $this->user->name . " removeu o grupo de segmentação " . $group->name;
                        $this->log->entity = "segmentation_group";
                        $this->log->entity_id = $group->id;
                        $this->log->insert();
                        return $this->response($response, 200, ["message" => "Grupo de segmentação removido!"]);
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
            $group = new SegmentationGroupModel();
            $groups = $group->getAll($search, $from, $offset);
            $total = $group->getTotal();
            $filtered = $group->getTotal($search);
            return $this->response($response, 200, ["draw" => $draw, "data" => $groups, "recordsTotal" => $total, "recordsFiltered" => $filtered]);
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
    public function groups(Request $request, Response $response) {
        if (Roles::isModOrUp($this->user)) {
            return $this->view($request)->render($response, 'segmentation-group\groups.html.twig', []);
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
            return $this->view($request)->render($response, 'segmentation-group\group.html.twig', ["current" => new SegmentationGroupModel()]);
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
            $current = new SegmentationGroupModel();
            $current = $current->getById($args["id"]);
            return $this->view($request)->render($response, 'segmentation-group\group.html.twig', ["current" => $current]);
        }
        return $this->response($response->withHeader('Location', '/admin'), 302);
    }
}