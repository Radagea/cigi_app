<?php

namespace CigiApp\Web\Controllers;

use Majframe\Web\Controllers\CoreController;
use Majframe\Web\Http\Response;

abstract class CoreApiController extends CoreController
{
    protected Response $response;

    public function onCreate()
    {
        $this->response = new Response(null, null, 200, Response::JSON);
    }

    protected function checkRequiredFields() : bool
    {
        if (!isset(static::$req_fields)) {
            return false;
        }

        foreach (static::$req_fields as $req_field) {
            if (!array_key_exists($req_field, $this->request->data)) {
                return false;
            }
        }
        return true;
    }

}
