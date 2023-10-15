<?php

namespace Majframe\Queue;

use Majframe\Core\Core;
use Majframe\Web\WebCore;

class QueueCore extends Core
{

    protected function __construct()
    {
        parent::__construct();
    }

    public static function getInstance(): QueueCore
    {
        if(self::$instance == null) {
            self::$instance = new WebCore();
        }

        return self::$instance;
    }

}