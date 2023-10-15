<?php

use Majframe\Web\Router\Router;

//Register
Router::addApiRoute('/auth/register', 'Auth\\Register', 'Register', [
    'POST' => 'registerAction'
]);

//Login
Router::addApiRoute('/auth/login', 'Auth\\Login', 'Login', [
    'POST' => 'loginAction'
]);