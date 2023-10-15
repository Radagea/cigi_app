<?php

namespace Majframe\Libs\DotEnv;

use Majframe\Libs\Exception\DotEnvException;

class DotEnv
{
    private static string $realpath;

    /**
     * @throws DotEnvException
     */
    public static function getEnv($path = __DIR__ . '/../../../.env')
    {
        static::$realpath = realpath($path);

        if (!is_file(static::$realpath)) {
            throw new DotEnvException('.env file is missing', 404);
        }

        if (!is_readable(static::$realpath)) {
            throw new DotEnvException('I can\'t read the .env file ');
        }

        $vars = [];

        if ($file = fopen(static::$realpath, 'r')) {
            while(!feof($file)) {
                $line = fgets($file);
                if ($line[0] != '#' && $line != PHP_EOL)
                {
                    $exploded = explode('=', $line);
                    $vars[trim($exploded[0])] = trim($exploded[1]);
                }
            }
        }

        if(!empty(array_diff(static::getRequired(), array_keys($vars)))) {
            throw new DotEnvException('Some of the required ENV variable missing');
        }

        return $vars;
    }

    public static function getRequired() : Array
    {
        return [
            'APP_ENV',
            'DEFAULT_WEB_SRC_NAMESPACE',
            'DB_MODE',
            'DB_HOST',
            'DB_USER',
            'DB_PASSWORD',
            'DB_NAME',
            'DB_PORT',
            'Q_HOST',
            'Q_PORT',
            'ROUTING_MODE'
        ];
    }
}