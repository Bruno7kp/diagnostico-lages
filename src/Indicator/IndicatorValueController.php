<?php


namespace App\Indicator;


use App\Base\Controller;
use App\Categories\CategoriesModel;
use App\Region\RegionModel;
use App\Segmentation\SegmentationModel;
use App\User\Roles;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class IndicatorValueController extends Controller
{
    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function register(Request $request, Response $response) {
        if (Roles::isDataOrUp($this->user)) {
            $body = $request->getParsedBody();
            $value = new IndicatorValueModel();

            if (array_key_exists("value", $body)) {
                $value->value = $body["value"];
            } else {
                return $this->response($response, 400, ["message" => "Preencha o valor do índice."]);
            }

            if (array_key_exists("indicator_period", $body)) {
                $value->indicator_period = $body["indicator_period"];
            } else {
                return $this->response($response, 400, ["message" => "Selecione o ano."]);
            }

            if (array_key_exists("description", $body) && !empty(trim($body["description"]))) {
                $value->description = $body["description"];
            } else {
                $value->description = "";
            }

            $indicator = new IndicatorModel();
            if (array_key_exists("indicator_id", $body) && $indicator = $indicator->getById($body["indicator_id"])) {
                $value->indicator_id = $indicator->id;
            } else {
                return $this->response($response, 400, ["message" => "Selecione o indicador."]);
            }

            $region = new RegionModel();
            if (array_key_exists("region_id", $body) && $region = $region->getById($body["region_id"])) {
                $value->region_id = $region->id;
            } else {
                return $this->response($response, 400, ["message" => "Selecione a região."]);
            }

            $segmentation = new SegmentationModel();
            if (array_key_exists("segmentation_id", $body) && $segmentation = $segmentation->getById($body["segmentation_id"])) {
                $value->segmentation_id = $segmentation->id;
            }
            if ($value->insert()) {
                $this->log->action = $this->user->name . " cadastrou o valor do índice";
                $this->log->entity = "indicator_value";
                $this->log->entity_id = $value->id;
                $this->log->insert();

                return $this->response($response, 201, [
                    "message" => "Valor do índice cadastrado!",
                    "data" => $value->getById($value->id)
                ]);
            } else {
                return $this->response($response, 500);
            }
        }
        return $this->response($response, 403);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function batchRegister(Request $request, Response $response) {
        if (Roles::isDataOrUp($this->user)) {
            $prebody = $request->getParsedBody();
            if (array_key_exists("values", $prebody) && is_array($prebody["values"])) {
                $list = [];
                $value = new IndicatorValueModel();
                foreach ($prebody["values"] as $body) {
                    $value = new IndicatorValueModel();

                    if (array_key_exists("value", $body)) {
                        $value->value = $body["value"];
                    } else {
                        return $this->response($response, 400, ["message" => "Preencha o valor do índice."]);
                    }

                    if (array_key_exists("indicator_period", $body)) {
                        $value->indicator_period = $body["indicator_period"];
                    } else {
                        return $this->response($response, 400, ["message" => "Selecione o ano."]);
                    }

                    if (array_key_exists("description", $body) && !empty(trim($body["description"]))) {
                        $value->description = $body["description"];
                    } else {
                        $value->description = "";
                    }

                    $indicator = new IndicatorModel();
                    if (array_key_exists("indicator_id", $body) && $indicator = $indicator->getById($body["indicator_id"])) {
                        $value->indicator_id = $indicator->id;
                    } else {
                        return $this->response($response, 400, ["message" => "Selecione o indicador."]);
                    }

                    $region = new RegionModel();
                    if (array_key_exists("region_id", $body) && $region = $region->getById($body["region_id"])) {
                        $value->region_id = $region->id;
                    } else {
                        return $this->response($response, 400, ["message" => "Selecione a região."]);
                    }

                    $segmentation = new SegmentationModel();
                    if (array_key_exists("segmentation_id", $body) && $segmentation = $segmentation->getById($body["segmentation_id"])) {
                        $value->segmentation_id = $segmentation->id;
                    }
                    $list[] = $value;
                }
                if ($value->batchInsert($list)) {
                    $this->log->action = $this->user->name . " cadastrou valores do índice";
                    $this->log->entity = "indicator_value";
                    $this->log->entity_id = $value->id;
                    $this->log->insert();

                    return $this->response($response, 201, [
                        "message" => "Valores do índice cadastrados!",
                        "data" => $value->getById($value->id)
                    ]);
                } else {
                    return $this->response($response, 500);
                }
            }
        }
        return $this->response($response, 403);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function update(Request $request, Response $response) {
        if (Roles::isDataOrUp($this->user)) {
            $body = $request->getParsedBody();
            $value = new IndicatorValueModel();

            if (array_key_exists("id", $body)) {
                if ($value = $value->getById($body["id"])) {
                    if (array_key_exists("value", $body)) {
                        $value->value = $body["value"];
                    } else {
                        return $this->response($response, 400, ["message" => "Preencha o valor do índice."]);
                    }

                    if (array_key_exists("indicator_period", $body)) {
                        $value->indicator_period = $body["indicator_period"];
                    } else {
                        return $this->response($response, 400, ["message" => "Selecione o ano."]);
                    }

                    if (array_key_exists("description", $body) && !empty(trim($body["description"]))) {
                        $value->description = $body["description"];
                    } else {
                        $value->description = "";
                    }

                    $indicator = new IndicatorModel();
                    if (array_key_exists("indicator_id", $body) && $indicator = $indicator->getById($body["indicator_id"])) {
                        $value->indicator_id = $indicator->id;
                    } else {
                        return $this->response($response, 400, ["message" => "Selecione o indicador."]);
                    }

                    $region = new RegionModel();
                    if (array_key_exists("region_id", $body) && $region = $region->getById($body["region_id"])) {
                        $value->region_id = $region->id;
                    } else {
                        return $this->response($response, 400, ["message" => "Selecione a região."]);
                    }

                    $segmentation = new SegmentationModel();
                    if (array_key_exists("segmentation_id", $body) && $segmentation = $segmentation->getById($body["segmentation_id"])) {
                        $value->segmentation_id = $segmentation->id;
                    }

                    if ($value->update()) {
                        $this->log->action = $this->user->name . " atualizou o valor do índice";
                        $this->log->entity = "indicator_value";
                        $this->log->entity_id = $value->id;
                        $this->log->insert();

                        return $this->response($response, 201, [
                            "message" => "Valor do índice atualizado!",
                            "data" => $value->getById($value->id)
                        ]);
                    } else {
                        return $this->response($response, 500);
                    }
                }
                return $this->response($response, 404);
            }
        }
        return $this->response($response, 403);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function batchUpdate(Request $request, Response $response) {
        if (Roles::isDataOrUp($this->user)) {
            $prebody = $request->getParsedBody();
            if (array_key_exists("values", $prebody) && is_array($prebody["values"])) {
                $list = [];
                $value = new IndicatorValueModel();

                foreach ($prebody["values"] as $body) {
                    $value = new IndicatorValueModel();
                    if (array_key_exists("id", $body)) {
                        if ($value = $value->getById($body["id"])) {
                            if (array_key_exists("value", $body)) {
                                $value->value = $body["value"];
                            } else {
                                return $this->response($response, 400, ["message" => "Preencha o valor do índice."]);
                            }

                            if (array_key_exists("indicator_period", $body)) {
                                $value->indicator_period = $body["indicator_period"];
                            } else {
                                return $this->response($response, 400, ["message" => "Selecione o ano."]);
                            }

                            if (array_key_exists("description", $body) && !empty(trim($body["description"]))) {
                                $value->description = $body["description"];
                            } else {
                                $value->description = "";
                            }

                            $indicator = new IndicatorModel();
                            if (array_key_exists("indicator_id", $body) && $indicator = $indicator->getById($body["indicator_id"])) {
                                $value->indicator_id = $indicator->id;
                            } else {
                                return $this->response($response, 400, ["message" => "Selecione o indicador."]);
                            }

                            $region = new RegionModel();
                            if (array_key_exists("region_id", $body) && $region = $region->getById($body["region_id"])) {
                                $value->region_id = $region->id;
                            } else {
                                return $this->response($response, 400, ["message" => "Selecione a região."]);
                            }

                            $segmentation = new SegmentationModel();
                            if (array_key_exists("segmentation_id", $body) && $segmentation = $segmentation->getById($body["segmentation_id"])) {
                                $value->segmentation_id = $segmentation->id;
                            }
                            $list[] = $value;
                        }
                    }
                }
                if ($value->batchUpdate($list)) {
                    $this->log->action = $this->user->name . " atualizou os valores do índice";
                    $this->log->entity = "indicator_value";
                    $this->log->entity_id = $value->id;
                    $this->log->insert();

                    return $this->response($response, 201, [
                        "message" => "Valores do índice atualizados!",
                        "data" => $value->getById($value->id)
                    ]);
                } else {
                    return $this->response($response, 500);
                }
            }
        }
        return $this->response($response, 403);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function search(Request $request, Response $response) {
        $body = $request->getParsedBody();
        $value = new IndicatorValueModel();
        $year = $indicator_id = $region_id = $segmentation_id = null;
        if (array_key_exists("year", $body)) {
            $year = $body["year"];
        }
        if (array_key_exists("indicator_id", $body)) {
            $indicator_id = $body["indicator_id"];
        }
        if (array_key_exists("region_id", $body)) {
            $region_id = $body["region_id"];
        }
        if (array_key_exists("segmentation_id", $body)) {
            $segmentation_id = $body["segmentation_id"];
        }
        return $this->response($response, 200, ["data" => $value->getByFilter($year, $indicator_id, $region_id, $segmentation_id)]);
    }
}
