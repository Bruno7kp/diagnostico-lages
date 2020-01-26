<?php


namespace App\Base;


use App\Log\LogModel;
use App\User\Auth;
use App\User\UserModel;
use Psr\Http\Message\ResponseInterface as Response;

class Controller
{
    /**
     * @var Auth|null
     */
    protected $auth = null;

    /**
     * @var UserModel|null
     */
    protected $user = null;

    /**
     * @var LogModel|null
     */
    protected $log = null;

    public function __construct()
    {
        $this->auth = new Auth();
        $this->user = $this->auth->getCurrentUser();
        if ($this->user == null)
            $this->user = new UserModel();
        $this->log = new LogModel();
        $this->log->user_id = $this->user->id;
    }

    /**
     * @param Response $response
     * @param int $code
     * @param array $data
     * @return Response
     */
    public function response(Response $response, $code, $data = []) {
        $response = $response->withHeader('Content-Type', 'application/json')->withStatus($code);
        switch ($code) {
            case 500:
                if (empty($data)) {
                    $data = [
                        'message' => 'Não foi possível completar a operação, tente novamente mais tarde.'
                    ];
                }
                break;
            case 404:
                if (empty($data)) {
                    $data = [
                        'message' => 'Página ou recurso não encontrado.'
                    ];
                }
                break;
            case 403:
                if (empty($data)) {
                    $data = [
                        'message' => 'Você não tem permissão para acessar este serviço.'
                    ];
                }
                break;
        }
        $payload = json_encode($data);
        $response->getBody()->write($payload);

        return $response;
    }
}
