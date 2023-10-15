<?php

use Majframe\Web\Router\Router;

Router::getRouteByName('404')->setController('errorController@action404');

//users
Router::addApiRoute('/user', 'UsersController', 'CurrentUser', [
    'GET' => 'getCurrentUserData'
]);

Router::addApiRoute('/user/{id_user}', 'UsersController', 'OtherUser', [
    'GET' => 'getById',
]);

//Groups
Router::addApiRoute('/group/create', 'GroupsController', 'GroupCreate', [
    'POST' => 'createGroup'
]);

Router::addApiRoute('/group/join', 'GroupsController', 'GroupJoin', [
    'POST' => 'joinGroup'
]);

Router::addApiRoute('/group/leave', 'GroupsController', 'GroupLeave', [
    'DELETE' => 'leaveGroup'
]);

Router::addApiRoute('/group/delete', 'GroupsController', 'GroupDelete', [
    'DELETE' => 'deleteGroup'
]);

Router::addApiRoute('/group', 'GroupsController', 'GroupList', [
    'GET' => 'getGroups'
]);

Router::addApiRoute('/group/{group_id}', 'GroupsController', 'GroupData', [
    'GET' => 'getGroupData'
]);