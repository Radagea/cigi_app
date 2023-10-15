<?php

namespace CigiApp\Web\Controllers\Auth;

use CigiApp\Web\Controllers\CoreApiController;
use CigiApp\Web\Models\Token;
use CigiApp\Web\Models\User;
use Majframe\Web\Controllers\CoreController;
use Majframe\Web\Http\Response;

class Register extends CoreApiController
{
    protected static array $req_fields = [
        'email',
        'password',
        'full_name'
    ];

    public function __construct()
    {
        $this->response = new Response(null, null, 406, Response::JSON);
    }

    public function registerAction() : Response
    {
        try {
            if ($this->request->data === null) {
                throw new \Exception('The request body is empty!', 400);
            }

            if (!$this->checkRequiredFields()) {
                throw new \Exception('The request body does not have the required fields', 400);
            }

            if (!$this->validateEmail($this->request->data['email'])) {
                throw new \Exception('The email is not valid!', 100);
            }

            if (User::count([['field' => 'email', 'value' => $this->request->data['email']]]) > 0) {
                throw new \Exception('This email is already registered', 200);
            }

            if (!$this->doRegistration()) {
                throw new \Exception('Something went wrong', 1);
            }

        } catch (\Exception $e) {
            $this->response->vars = [
                'message' => $e->getMessage(),
                'error' => $e->getCode()
            ];
        }

        return $this->response;
    }

    private function doRegistration() : bool {
        $user = new User();
        $user->email = $this->request->data['email'];
        $user->full_name = $this->request->data['full_name'];
        $user->password = password_hash($this->request->data['password'], PASSWORD_DEFAULT);

        if (!$user->save()) {
            return false;
        }

        $token = Token::getToken($user);

        if (!$token) {
            return false;
        }

        $this->response->vars['message'] = 'Success';
        $this->response->vars['auth_token'] = $token->token;
        $this->response->vars['exp_date'] = $token->expiration_date;

        $this->response->setResponseCode(201);

        return true;
    }

    private function validateEmail(String $email) : bool
    {
        if (str_contains($email, '@') && str_contains($email, '.')) {
            return true;
        }

        return false;
    }
}