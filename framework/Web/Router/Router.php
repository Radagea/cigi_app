<?php

namespace Majframe\Web\Router;

final class Router
{
    public String $currentPath;
    protected Array $routes;
    private static Router|null $instance = null;
    private Route $currentRoute;
    private String $currentUri;

    private function __construct() {}

    public static function getInstance()
    {
        if (static::$instance == null) {
            static::$instance = new Router();

            static::$instance->routes['404'] = new Route(Route::NO_ROUTE, 'errorController', '404');
        }

        return static::$instance;
    }

    public static function addRoute(String $path, String $controller, String $name) : void
    {
        (static::getInstance())->routes[$name] = new Route($path, $controller, $name);
    }

    /**
     * @param String $path  - the uri path for the Route
     * @param String $controller - The controller namespace without action designation!!
     * @param String $name - The route name
     * @param array $methods_action - The methods action the array key should be the HTTP Method name the value should be the action (correct function name which can be found in the controller), if a method not in the array that method will be disabled.
     * Example: $methods_action = ['POST' => 'postAction', 'GET' => 'getAction']
     * @return void
     */
    public static function addApiRoute(String $path, String $controller, String $name, Array $methods_action) : void
    {
        $route = new Route($path, $controller, $name);
        $route->setApiMode($methods_action);
        (static::getInstance())->routes[$name] = $route;
    }

    /** TODO
     * $methods = [
     *      'GET' => [
     *          'sub_route' => '/get',
     *          'action' => 'getAction',
     *          'name'   => 'Route Name',
     *      ]
     * ]
     *
     * @param String $main_path
     * @param String $controller
     * @param String $name
     * @param array $methods
     * @return void
     */
    public static function addApiRouteGroup(String $main_path, String $controller, String $name, Array $methods): void {

    }

    public static function getRouteByName(String $name) : Route
    {
        return (static::getInstance())->routes[$name];
    }

    public function findRouteByUri(String $uri) : Route
    {
        $this->currentUri = $uri;
        $this->currentRoute = Router::getRouteByName('404');
        /** @var Route $route */
        foreach ($this->routes as $route) {
            if ($route->name == '404') {
                continue;
            }
            if ($route->compareUri($uri)) {
                $this->currentRoute = $route;
                break;
            }
        }

        return $this->currentRoute;
    }

    public static function getCurrentRoute() : Route
    {
        return (self::getInstance())->currentRoute;
    }

    public static function getCurrentUri() : String
    {
        return (self::getInstance())->currentUri;
    }
}