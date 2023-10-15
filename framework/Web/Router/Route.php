<?php

namespace Majframe\Web\Router;

use Majframe\Web\WebCore;

class Route
{
    const NO_ROUTE = 'NO_ROUTE';
    public String $name;
    private Array $explodedPath;
    private Array $availableParams;
    private String $controllerNamespace;
    private String $controllerAction = 'indexAction';
    private Array $apiMethods;
    private bool $isApi = false;

    public function __construct(String $path, String $controller, String $name)
    {
        $this->name = $name;
        $this->isApi = false;
        $nameSpace = (WebCore::getInstance())->getEnv()['DEFAULT_WEB_SRC_NAMESPACE'];

        if ($nameSpace[strlen($nameSpace)-1] != '\\') {
            $nameSpace[strlen($nameSpace)] = '\\';
        }

        $controller = explode('@', $controller);

        if (isset($controller[1])) {
            $this->controllerAction = $controller[1];
        }

        $this->controllerNamespace = $nameSpace . 'Web\\Controllers\\' . $controller[0];

        if ($path != self::NO_ROUTE) {
            $exploded_paths = explode('/', ltrim($path, $path[0]));
            foreach ($exploded_paths as $key => $pathElement) {
                $type = 'uri';
                if (str_contains($pathElement, '{') && str_contains($pathElement, '}')) {
                    $paramName = str_replace(['{', '}'], '', $pathElement);
                    $this->availableParams[$paramName] = [
                        'pos' => $key,
                    ];

                    $type = 'param';
                }

                $this->explodedPath[] = [
                    'elem' =>$pathElement,
                    'type' => $type
                ];
            }
        }

    }

    public function compareUri($uri) : bool
    {
        $uri = explode('/', ltrim($uri, $uri[0]));

        if (sizeof($uri) === sizeof($this->explodedPath)) {
            $match = 0;

            foreach ($this->explodedPath as $key => $path) {
                if ($path['type'] == 'param') {
                    $match++;
                } elseif ($path['elem'] == $uri[$key]) {
                    $match++;
                }
            }

            if ($match === sizeof($this->explodedPath)) {
                if (isset($this->availableParams)) {
                    foreach ($this->availableParams as $key => $availableParam) {
                        $this->availableParams[$key]['value'] = $uri[$availableParam['pos']];
                    }
                }

                return true;
            }
        }

        return false;
    }
    public function setController(String $controller)
    {
        $controller = explode('@', $controller);

        if (isset($controller[1])) {
            $this->controllerAction = $controller[1];
        }
    }

    public function setApiMode(Array $api_actions) : void
    {
        $this->isApi = true;
        $this->apiMethods = $api_actions;
    }

    public function getApiMethodActions() : Array
    {
        return $this->apiMethods;
    }
    public function isApi() : bool
    {
        return $this->isApi;
    }

    public function getControllerNamespace() : String
    {
        return $this->controllerNamespace;
    }

    public function getControllerAction(): String
    {
        return $this->controllerAction;
    }

    public function getParam(String $param) : String|int|false
    {
        if (isset($this->availableParams[$param])) {
            return $this->availableParams[$param]['value'];
        }

        return false;
    }
}