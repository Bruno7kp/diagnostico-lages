<?php


namespace App\Indicator;


use App\Base\Controller;
use App\Categories\CategoriesModel;
use App\Region\RegionModel;
use App\Segmentation\SegmentationGroupModel;
use App\User\Roles;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

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
        $args = $request->getQueryParams();
        $search = "%" . $args["search"]["value"] . "%";
        $draw = intval($args["draw"]);
        $from = intval($args["start"]);
        $offset = intval($args["length"]);
        $indicator = new IndicatorModel();
        $indicators = $indicator->getAll($search, $from, $offset);
        $total = $indicator->getTotal();
        $filtered = $indicator->getTotal($search);
        return $this->response($response, 200, ["draw" => $draw, "data" => $indicators, "recordsTotal" => $total, "recordsFiltered" => $filtered]);
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

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws \ReflectionException
     */
    public function indicators(Request $request, Response $response) {
        if (Roles::isDataOrUp($this->user)) {
            return $this->view($request)->render($response, 'indicator\indicators.html.twig', []);
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
            $groups = new IndicatorGroupModel();
            $groups = $groups->getFullList();
            $types = IndicatorType::get();
            $segmentations = new SegmentationGroupModel();
            $segmentations = $segmentations->getFullList();
            return $this->view($request)->render($response, 'indicator\indicator.html.twig', [
                "current" => new IndicatorModel(),
                "groups" => $groups,
                "types" => $types,
                "segmentations" => $segmentations,
            ]);
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
            $current = new IndicatorModel();
            $current = $current->getById($args["id"]);
            $groups = new IndicatorGroupModel();
            $groups = $groups->getFullList();
            $types = IndicatorType::get();
            $segmentations = new SegmentationGroupModel();
            $segmentations = $segmentations->getFullList();
            return $this->view($request)->render($response, 'indicator\indicator.html.twig', [
                "current" => $current,
                "groups" => $groups,
                "types" => $types,
                "segmentations" => $segmentations,
            ]);
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
    public function indicadores(Request $request, Response $response) {
        $category = new CategoriesModel();
        $categories = $category->getFullList(true);
        $seg = new SegmentationGroupModel();
        $segmentations = $seg->getFullList();
        return $this->view($request)->render($response, 'indicator\public.indicators.html.twig', ["categories" => $categories, "segmentation" => $segmentations]);
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
    public function indicador(Request $request, Response $response, $args) {
        if (!array_key_exists("id", $args))
            return $this->response($response, 404);

        $group = new IndicatorGroupModel();
        $group = $group->getById($args["id"]);
        $groups = $group->getAllByCategory($group->categories_id, true);
        $periods = $group->periods();
        $period = $periods[0]["indicator_period"];

        if (array_key_exists("period", $args))
            $period = $args["period"];

        $region = new RegionModel();
        $regions = $region->getFullList();
        $category = new CategoriesModel();
        $category = $category->getById($group->categories_id);
        $category->groups = $groups;
        $seg = new SegmentationGroupModel();
        $segmentations = $seg->getFullList();

        $vals = $group->getByFilter($period, $group->id);
        $validRegions = [];
        foreach ($vals as $val) {
            foreach ($val->indicators as $indicator) {
                $validRegions = array_merge($validRegions, $indicator->validRegions);
            }
        }

        return $this->view($request)->render($response, 'indicator\public.indicator.html.twig', [
            "current" => $group,
            "regions" => $regions,
            "period" => $period,
            "periods" => $periods,
            "values" => $vals,
            "categories" => [$category],
            "segmentation" => $segmentations,
            "validRegions" => $validRegions
        ]);
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
    public function indice(Request $request, Response $response, $args) {
        if (!array_key_exists("id", $args))
            return $this->response($response, 404);

        $indicator = new IndicatorModel();
        $indicator = $indicator->getById($args["id"]);
        $periods = $indicator->periods();
        $period = $periods[0]["indicator_period"];
        $periods = array_reverse($periods);

        if (array_key_exists("period", $args))
            $period = $args["period"];

        $regionModel = new RegionModel();
        $regions = $regionModel->getFullList();
        $vals = [];
        $validPeriods = [];
        $validRegions = [];
        foreach ($regions as $region) {
            $vals[] = $indicator->getYearlyRegionValue($periods, $region);
            $validPeriods = array_merge($validPeriods, $indicator->validPeriods);
            $validRegions = array_merge($validRegions, $indicator->validRegions);
        }

        $group = new IndicatorGroupModel();
        $group = $group->getById($indicator->indicator_group_id);
        $groups = $group->getAllByCategory($group->categories_id, true);
        $category = new CategoriesModel();
        $category = $category->getById($group->categories_id);
        $category->groups = $groups;
        $seg = new SegmentationGroupModel();
        $segmentations = $seg->getFullList();


        return $this->view($request)->render($response, 'indicator\public.indexes.html.twig', [
            "current" => $indicator,
            "regions" => $regions,
            "period" => $period,
            "periods" => $periods,
            "values" => $vals,
            "categories" => [$category],
            "segmentation" => $segmentations,
            "validPeriods" => $validPeriods,
            "validRegions" => $validRegions
        ]);
    }
}
