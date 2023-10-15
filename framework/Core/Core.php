<?php

namespace Majframe\Core;

use Majframe\Libs\DotEnv\DotEnv;
use Majframe\Libs\Functions\ExecutionTime;
use Majframe\Queue\QueueCore;
use Majframe\Web\WebCore;

class Core
{
    protected static WebCore|Core|null $instance = null;
    public string $app_env;
    private Array $env;

    protected function __construct()
    {
        foreach (DotEnv::getEnv() as $key => $env) {
            $this->env[$key] = trim($env);
        }
        $this->app_env = $this->env['APP_ENV'];
    }

    final public static function getExistingInstance() : WebCore|false
    {
        if (self::$instance instanceof WebCore) {
            return self::$instance;
        }

        if (self::$instance instanceof QueueCore) {
            return self::$instance;
        }

        return false;
    }

    final public function getEnv() : Array
    {
        return $this->env;
    }
}
