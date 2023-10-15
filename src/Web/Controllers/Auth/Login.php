<?php

namespace CigiApp\Web\Controllers\Auth;

use CigiApp\Web\Controllers\CoreApiController;
use CigiApp\Web\Models\Token;
use CigiApp\Web\Models\User;
use Majframe\Web\Http\Response;

class Login extends CoreApiController
{

    protected static array $req_fields = [
        'email',
        'password',
    ];

    public function loginAction() : Response
    {
        try {
            if ($this->request->data === null) {
                throw new \Exception('The request body is empty!', 400);
            }

            if (!$this->checkRequiredFields()) {
                throw new \Exception('The request body does not have the required fields', 400);
            }

            /** @var User $user */
            if (!($user = User::get([['field' => 'email', 'value' => $this->request->data['email']]]))) {
                throw new \Exception('Wrong username or password', 401);
            }

            if (!$user->verifyPassword($this->request->data['password'])) {
                throw new \Exception('Wrong username or password', 401);
            }

            if (!($token = Token::getToken($user))) {
                throw new \Exception('Something went wrong connect with the developer', 1);
            }

            $this->response->vars = [
                'message' => 'Success',
                'user_id' => $user->id,
                'auth_token' => $token->token,
                'exp_date' => $token->expiration_date,
            ];

        } catch (\Exception $e) {
            $this->response->vars = [
                'message' => $e->getMessage(),
                'error' => $e->getCode()
            ];
        }

        return $this->response;
    }
}