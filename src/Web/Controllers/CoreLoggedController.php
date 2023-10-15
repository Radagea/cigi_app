<?php

namespace CigiApp\Web\Controllers;

use CigiApp\Web\Libs\AuthError;
use CigiApp\Web\Models\Token;
use CigiApp\Web\Models\User;

class CoreLoggedController extends CoreApiController
{

    protected User $user;
    protected AuthError $auth_error;

    public function onCreate()
    {
        parent::onCreate();

        try {
            if (!($bearer = $this->request->getBearer())) {
                throw new AuthError('Bearer token is missing', 1001);
            }

            $token = new Token();
            $token->token = $bearer;

            if (!$token->getByField('token')) {
                throw new AuthError('Bearer token is incorrect', 1002);
            }

            if (!$token->checkToken()) {
                throw new AuthError('Bearer token is expired ', 1003);
            }

            $this->user = new User();
            $this->user->id = $token->id_user;

            if (!$this->user->getByField('id')) {
                throw new AuthError('Bearer token is incorrect', 1004);
            }

            $this->response->vars['auth'] = [
                'auth_token' => $token->token,
                'exp_date' => $token->expiration_date
            ];

        } catch(AuthError $ae) {
            $this->auth_error = $ae;
        }
    }

    protected function exceptionHandler(\Exception $e) : void
    {
        if ($e instanceof AuthError) {
            $this->response->setResponseCode(401);
        }

        $this->response->vars = [
            'message' => $e->getMessage(),
            'error'   => $e->getCode(),
        ];
    }
}
