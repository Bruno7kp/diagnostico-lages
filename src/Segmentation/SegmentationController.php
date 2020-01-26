<?php


namespace App\Segmentation;


use App\Base\Controller;
use App\User\Roles;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class SegmentationController extends Controller
{
    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function register(Request $request, Response $response) {
        if (Roles::isModOrUp($this->user)) {
            $body = $request->getParsedBody();
            $segmentation = new SegmentationModel();

            if (array_key_exists("name", $body) && !empty(trim($body["name"]))) {
                $segmentation->name = $body["name"];
            } else {
                return $this->response($response, 400, ["message" => "Preencha o nome do segmento."]);
            }

            if (array_key_exists("description", $body) && !empty(trim($body["description"]))) {
                $segmentation->description = $body["description"];
            } else {
                $segmentation->description = "";
            }

            $group = new SegmentationGroupModel();
            if (array_key_exists("segmentation_group_id", $body) && $group = $group->getById($body["segmentation_group_id"])) {
                $segmentation->segmentation_group_id = $group->id;
            } else {
                return $this->response($response, 400, ["message" => "Preencha o grupo de segmentos."]);
            }

            if ($segmentation->insert()) {
                $this->log->action = $this->user->name . " cadastrou o segmento " . $segmentation->name;
                $this->log->entity = "segmentation";
                $this->log->entity_id = $segmentation->id;
                $this->log->insert();

                return $this->response($response, 201, [
                    "message" => "Segmento cadastrado!",
                    "data" => $segmentation->getById($segmentation->id)
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
            $segmentation = new SegmentationModel();

            if (array_key_exists("id", $body)) {
                $segmentation = $segmentation->getById($body["id"]);
                if ($segmentation !== null) {
                    if (array_key_exists("name", $body) && !empty(trim($body["name"]))) {
                        $segmentation->name = $body["name"];
                    } else {
                        return $this->response($response, 400, ["message" => "Preencha o nome do segmento."]);
                    }

                    if (array_key_exists("description", $body)) {
                        $segmentation->description = $body["description"];
                    }

                    $group = new SegmentationGroupModel();
                    if (array_key_exists("segmentation_group_id", $body) && $group = $group->getById($body["segmentation_group_id"])) {
                        $segmentation->segmentation_group_id = $group->id;
                    } else {
                        return $this->response($response, 400, ["message" => "Preencha o grupo de segmentos."]);
                    }

                    if ($segmentation->update()) {
                        $this->log->action = $this->user->name . " editou o segmento " . $segmentation->name;
                        $this->log->entity = "segmentation";
                        $this->log->entity_id = $segmentation->id;
                        $this->log->insert();
                        return $this->response($response, 200, ["message" => "Segmento atualizado!"]);
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
            $segmentation = new SegmentationModel();

            if (array_key_exists("id", $body)) {
                $segmentation = $segmentation->getById($body["id"]);
                if ($segmentation) {
                    if ($segmentation->delete()) {
                        $this->log->action = $this->user->name . " removeu o segmento " . $segmentation->name;
                        $this->log->entity = "segmentation";
                        $this->log->entity_id = $segmentation->id;
                        $this->log->insert();
                        return $this->response($response, 200, ["message" => "Segmento removido!"]);
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
        $segmentation = new SegmentationModel();

        return $this->response($response, 200, ["data" => $segmentation->getAll()]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function byGroup(Request $request, Response $response) {
        $segmentation = new SegmentationModel();
        $body = $request->getParsedBody();
        $group = new SegmentationGroupModel();

        if (array_key_exists("id", $body) && $group = $group->getById($body["id"])) {
            return $this->response($response, 200, ["data" => $segmentation->getAllByGroup($group->id)]);
        } else {
            return $this->response($response, 404);
        }
    }
}
