<?php

namespace Majframe\Web;

use Majframe\Core\Core;
use Majframe\Db\Connector;
use Majframe\Libs\Exception\MajException;
use Majframe\Web\Controllers\Controller;
use Majframe\Web\Controllers\CoreController;
use Majframe\Web\Http\Request;
use Majframe\Web\Http\Response;
use Majframe\Web\Router\Route;
use Majframe\Web\Router\Router;

final class WebCore extends Core
{
    private Router $router;
    private Connector $connector;

    public static function getInstance(): WebCore
    {
        if(self::$instance == null) {
            self::$instance = new WebCore();

            self::$instance->router = Router::getInstance();
            self::$instance->loadRoutes();
            self::$instance->connector = Connector::getConnector();

        }

        return self::$instance;
    }

    public static function startWeb() : void
    {
        include_once __DIR__ . '/../Libs/Functions/Function.php';

        try {
            (self::getInstance())->controllerInjector(self::$instance->router->findRouteByUri($_SERVER['REQUEST_URI']));
        } catch (MajException $e) {
            echo $e->getMessage();
            echo $e->getCode();
        }
    }

    private function loadRoutes()
    {
        $path = __DIR__ . '/../../src/Web/Routes';
        $files = array_diff(scandir($path), ['..', '.']);

        foreach ($files as $file) {
            include_once $path . '/' . $file;
        }
    }

    private function controllerInjector(Route $route) : void
    {
        $controller = $route->getControllerNamespace();
        $action = $route->getControllerAction();
        /** @var CoreController $controller */
        $controller = new $controller();

        if (!($controller instanceof CoreController)) {
            throw new MajException('The controller named: ' . $controller::class . ' not instance of the Controller class');
        }

        $request = new Request();

        if (!$request->loadData()) {
           $response = new Response(['err' => true, 'Message' => 'The content is not valid!'], null, 406, Response::JSON);
        }

        if (!isset($response)) {
            $controller->setRequest($request);

            if ($route->isApi()) {
                $enabled_methods = $route->getApiMethodActions();
                if (key_exists($request->getMethod(), $enabled_methods)) {
                    $action = $enabled_methods[$request->getMethod()];
                } else {
                    $action = 'methodNotEnabled';
                }
            }

            if (method_exists($controller, 'onCreate')) {
                $controller->onCreate();
            }

            /** @var Response $response */
            $response = $controller->$action();
        }


        if (!($response instanceof Response)) {
            throw new MajException('The controller (' . $controller::class . ') function (' . $action . ') value has bad return type. The correct is: Response');
        }

        http_response_code($response->getResponseCode());

        foreach ($response->getHeaders() as $key => $header) {
            header($key . $header);
        }

        if ($response->getContentType() === Response::JSON) {
            if ($this->app_env === 'dev' ) {
                $response->vars['dev'] = [
                        'peak_memory_usage' => memory_get_peak_usage()/1024 . 'KB',
                        'execution_time' => (microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']) * 60 . ' ms'
                    ];
            }

            echo json_encode($response->vars);
        }

    }

}