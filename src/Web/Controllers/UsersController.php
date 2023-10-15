<?php

namespace CigiApp\Web\Controllers;

use CigiApp\Web\Models\User;
use Majframe\Web\Http\Response;

class UsersController extends CoreLoggedController
{
    public function getCurrentUserData() : Response
    {
        try {
            if (isset($this->auth_error)) {
                throw $this->auth_error;
            }

            $this->response->vars['message'] = 'Success';
            $this->response->vars['user'] = $this->user->getReturnableArray();

        } catch (\Exception $e) {
            $this->exceptionHandler($e);
        }

        return $this->response;
    }

    public function getById(): Response
    {
        try {
            if (isset($this->auth_error)) {
                throw $this->auth_error;
            }

            $user = new User();
            $user->id = $this->getParam('id_user');

            if (!$user->getByField('id')) {
                throw new \Exception('The user doesn\'t exist', 404);
            }

            $this->response->vars['user'] = $user->getReturnableArray();

        } catch (\Exception $e) {
            $this->exceptionHandler($e);
        }

        return $this->response;
    }
}