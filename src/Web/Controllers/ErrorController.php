<?php

namespace CigiApp\Web\Controllers;

use Majframe\Web\Controllers\CoreController;
use Majframe\Web\Http\Response;

class ErrorController extends CoreController
{

    public function action404() : Response
    {
        return new Response(['message' => 'Endpoint has been not found', 'code' => 404], null, 404, Response::JSON);
    }

}