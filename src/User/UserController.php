<?php


namespace App\User;


use App\Base\Controller;
use App\Log\LogModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class UserController extends Controller
{
    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function register(Request $request, Response $response) {
        if (Roles::isAdmin($this->user)) {
            $body = $request->getParsedBody();
            $user = new UserModel();

            if (array_key_exists("name", $body) && !empty(trim($body["name"]))) {
                $user->name = $body["name"];
            } else {
                return $this->response($response, 400, ["message" => "Preencha o nome do usuário."]);
            }

            if (array_key_exists("email", $body) && filter_var($body["email"], FILTER_VALIDATE_EMAIL)) {
                $check = $user->getByEmail($body["email"]);
                if ($check) {
                    return $this->response($response, 400, ["message" => "E-mail já utilizado."]);
                }
                $user->email = $body["email"];
            } else {
                return $this->response($response, 400, ["message" => "Preencha o email do usuário."]);
            }

            if (array_key_exists("password", $body) && !empty(trim($body["password"]))) {
                $user->password = $body["password"];
            } else {
                return $this->response($response, 400, ["message" => "Preencha a senha do usuário."]);
            }

            if (array_key_exists("role", $body) && !empty(trim($body["role"]))) {
                $user->role = $body["role"];
            } else {
                return $this->response($response, 400, ["message" => "Preencha o nível do usuário."]);
            }

            if ($user->insert()) {
                $this->log->action = $this->user->name . " cadastrou o usuário " . $user->name;
                $this->log->entity = "user";
                $this->log->entity_id = $user->id;
                $this->log->insert();

                return $this->response($response, 201, [
                    "message" => "Usuário cadastrado!",
                    "data" => $user->getById($user->id),
                    "redirect" => "/admin/user"
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
    public function update(Request $request, Response $response) {
        if (Roles::isDataOrUp($this->user)) {
            $body = $request->getParsedBody();
            $user = new UserModel();
            if (array_key_exists("id", $body)) {
                $user = $user->getById($body["id"]);
                if ($user !== null) {
                    if ($user->id == $this->user->id || Roles::isAdmin($this->user)) {
                        if (array_key_exists("name", $body) && !empty(trim($body["name"]))) {
                            $user->name = $body["name"];
                        } else {
                            return $this->response($response, 400, ["message" => "Preencha o nome do usuário."]);
                        }

                        if (array_key_exists("email", $body) && filter_var($body["email"], FILTER_VALIDATE_EMAIL)) {
                            $check = $user->getByEmail($body["email"]);
                            if ($check && $check->id != $user->id) {
                                return $this->response($response, 400, ["message" => "E-mail já utilizado."]);
                            }
                            $user->email = $body["email"];
                        } else {
                            return $this->response($response, 400, ["message" => "Preencha o email do usuário."]);
                        }

                        if (Roles::isAdmin($this->user)) {
                            if (array_key_exists("role", $body) && !empty(trim($body["role"]))) {
                                $user->role = $body["role"];
                            } else {
                                return $this->response($response, 400, ["message" => "Preencha o nível do usuário."]);
                            }
                        }

                        if ($user->update()) {
                            $this->log->action = $this->user->name . " editou o usuário " . $user->name;
                            $this->log->entity = "user";
                            $this->log->entity_id = $user->id;
                            $this->log->insert();
                            return $this->response($response, 200, ["message" => "Usuário atualizado!"]);
                        } else {
                            return $this->response($response, 500);
                        }
                    }
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
    public function updatePassword(Request $request, Response $response) {
        if (Roles::isDataOrUp($this->user)) {
            $body = $request->getParsedBody();
            $user = new UserModel();
            if (array_key_exists("id", $body) && array_key_exists("password", $body)) {
                $user = $user->getWithPasswordById($body["id"]);
                if ($user !== null) {
                    if ($user->id == $this->user->id && array_key_exists("old_password", $body)) {
                        if ($user->login($body["old_password"])) {
                            $user->password = $body["password"];
                            if ($user->updatePassword()) {
                                $this->log->action = $this->user->name . " atualizou a senha do usuário " . $user->name;
                                $this->log->entity = "user";
                                $this->log->entity_id = $user->id;
                                $this->log->insert();
                                return $this->response($response, 200, ["message" => "Senha atualizada!"]);
                            }
                            return $this->response($response, 500);
                        }
                        return $this->response($response, 400, ["message" => "Senha atual incorreta."]);
                    } else if ($user->id != $this->user->id && Roles::isAdmin($this->user)) {
                        $user->password = $body["password"];
                        if ($user->updatePassword()) {
                            $this->log->action = $this->user->name . " atualizou a senha do usuário " . $user->name;
                            $this->log->entity = "user";
                            $this->log->entity_id = $user->id;
                            $this->log->insert();
                            return $this->response($response, 200, ["message" => "Senha atualizada!"]);
                        }
                    }
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
    public function remove(Request $request, Response $response) {
        if (Roles::isAdmin($this->user)) {
            $body = $request->getParsedBody();
            $user = new UserModel();
            if (array_key_exists("id", $body)) {
                $user = $user->getById($body["id"]);
                if ($user && !Roles::isAdmin($user)) {
                    if ($user->delete()) {
                        $this->log->action = $this->user->name . " removeu o usuário " . $user->name;
                        $this->log->entity = "user";
                        $this->log->entity_id = $user->id;
                        $this->log->insert();
                        return $this->response($response, 200, ["message" => "Usuário removido!"]);
                    }
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
    public function login(Request $request, Response $response) {
        if (Roles::isGuest($this->user)) {
            $body = $request->getParsedBody();
            $user = new UserModel();

            if (array_key_exists("email", $body) && filter_var($body["email"], FILTER_VALIDATE_EMAIL)) {
                $user = $user->getByEmail($body["email"]);
                if (array_key_exists("password", $body) && $user && $user->login($body["password"])) {
                    $this->auth->startSession($user);
                    $this->log->action = $user->name . " fez login";
                    $this->log->entity = "user";
                    $this->log->entity_id = $user->id;
                    $this->log->user_id = $this->user->id;
                    $this->log->insert();
                    return $this->response($response, 200, ["message" => "Login realizado com sucesso!"]);
                } else {
                    return $this->response($response, 400, ["message" => "O e-mail e a senha fornecidos não correspondem às informações em nossos registros. Verifique-as e tente novamente."]);
                }
            }
            return $this->response($response, 400, ["message" => "Preencha o email do usuário."]);
        }
        return $this->response($response, 403);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function logout(Request $request, Response $response) {
        if (Roles::isDataOrUp($this->user)) {
            $this->auth->endSession();
            $this->log->action = $this->user->name . " fez logout";
            $this->log->entity = "user";
            $this->log->entity_id = $this->user->id;
            $this->log->user_id = $this->user->id;
            $this->log->insert();
            return $this->response($response, 200);
        } else {
            return $this->response($response, 403);
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function all(Request $request, Response $response) {
        if (Roles::isAdmin($this->user)) {
            $args = $request->getQueryParams();
            $search = "%" . $args["search"]["value"] . "%";
            $draw = intval($args["draw"]);
            $from = intval($args["start"]);
            $offset = intval($args["length"]);
            $user = new UserModel();
            $users = $user->getAll($search, $from, $offset);
            $total = $user->getTotal();
            $filtered = $user->getTotal($search);
            foreach ($users as &$user) {
                $user[2] = Roles::text($user[2]);
            }
            return $this->response($response, 200, ["draw" => $draw, "data" => $users, "recordsTotal" => $total, "recordsFiltered" => $filtered]);
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
    public function users(Request $request, Response $response) {
        if (Roles::isAdmin($this->user)) {
            return $this->view($request)->render($response, 'user\users.html.twig', []);
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
        if (Roles::isAdmin($this->user)) {
            return $this->view($request)->render($response, 'user\user.html.twig', ["current" => new UserModel()]);
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
        if (Roles::isAdmin($this->user) || $args["id"] == $this->user->id) {
            $current = new UserModel();
            $current = $current->getById($args["id"]);
            return $this->view($request)->render($response, 'user\user.html.twig', ["current" => $current]);
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
    public function editPassword(Request $request, Response $response, $args) {
        if (Roles::isAdmin($this->user) || $args["id"] == $this->user->id) {
            $current = new UserModel();
            $current = $current->getById($args["id"]);
            return $this->view($request)->render($response, 'user\password.html.twig', ["current" => $current]);
        }
        return $this->response($response->withHeader('Location', '/admin'), 302);
    }
}
