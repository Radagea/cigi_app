<?php

namespace CigiApp\Web\Controllers;

use CigiApp\Web\Models\Group;
use CigiApp\Web\Models\User;
use Majframe\Web\Http\Response;

class GroupsController extends CoreLoggedController
{

    public function createGroup() : Response
    {
        try {
            if (isset($this->auth_error)) {
                throw $this->auth_error;
            }

            if (!$this->checkGroupRequired($this->request->getRoute()->name)) {
                throw new \Exception('The request body does not have the required fields', 400);
            }

            $group = new Group($this->request->data['group_name'], $this->user->id);

            $this->response->vars['message'] = 'Success';
            $this->response->vars['group'] = $group->getAsArray(true);


        } catch (\Exception $e) {
            $this->exceptionHandler($e);
        }

        return $this->response;
    }

    public function joinGroup() : Response
    {
        try {
            if (isset($this->auth_error)) {
                throw $this->auth_error;
            }

            if (!$this->checkGroupRequired($this->request->getRoute()->name)) {
                throw new \Exception('The request body does not have the required fields', 400);
            }

            $group = new Group();
            $group->group_code = $this->request->data['group_code'];

            if (!$group->getByField('group_code')) {
                throw new \Exception('Group not exist!', 404);
            }

            if (!$group->joinGroup($this->user->id)) {
                throw new \Exception('This user already joined this group', 200);
            }

            //Todo later Connector model
            $this->response->vars['message'] = 'Success';
            $this->response->vars['group'] = $group->getAsArray(true);


        } catch (\Exception $e) {
            $this->exceptionHandler($e);
        }

        return $this->response;
    }

    public function deleteGroup() : Response
    {
        try {
            if (isset($this->auth_error)) {
                throw $this->auth_error;
            }

            if (!$this->checkGroupRequired($this->request->getRoute()->name)) {
                throw new \Exception('The request body does not have the required fields', 400);
            }

            $group = new Group();
            $group->id = $this->request->data['group_id'];

            if (!$group->getByField('id')) {
                throw new \Exception('The group is not exist!', 404);
            }

            if ($group->id_user_owner != $this->user->id) {
                throw new \Exception('The logged user is not the owner of the group', 401);
            }

            if (!$group->deleteG()) {
                throw new \Exception('Something went wrong', 400);
            }

            $this->response->vars['message'] = 'Success';

        } catch (\Exception $e) {
            $this->exceptionHandler($e);
        }

        return $this->response;
    }

    public function getGroups() : Response
    {
        try {
            if (isset($this->auth_error)) {
                throw $this->auth_error;
            }

            if (!($groups = Group::getUserGroups($this->user->id))) {
                throw new \Exception('Something went wrong getUserGroups', 400);
            }

            $this->response->vars['message'] = 'Success';
            $this->response->vars['groups'] = $groups;

        }catch (\Exception $e) {
            $this->exceptionHandler($e);
        }


        return $this->response;
    }

    public function getGroupData() : Response
    {

        try {
            if (isset($this->auth_error)) {
                throw $this->auth_error;
            }

            $group = new Group();
            $group->id = $this->getParam('group_id');

            if (!$group->getByField('id')) {
                throw new \Exception('Something went wrong - get group', 400);
            }

            if ($group->isIn($this->user->id) < 1) {
                throw new \Exception('You are not in this group', 400);
            }

            $this->response->vars['group'] = $group->getAsArray(true, true);
        } catch (\Exception $e) {
            $this->exceptionHandler($e);
        }


        return $this->response;
    }

    public function leaveGroup() : Response
    {
        try {
            if (isset($this->auth_error)) {
                throw $this->auth_error;
            }

            if (!$this->checkGroupRequired($this->request->getRoute()->name)) {
                throw new \Exception('The required field is missing from the request!', 401);
            }

            $group = new Group();
            $group->id = $this->request->data['group_id'];

            if (!$group->getByField('id')) {
                throw new \Exception('The group doesn\'t exist!', 404);
            }

            if ($group->isIn($this->user->id) < 1) {
                throw new \Exception('You can\'t leave from this group!', 401);
            }

            $group->leave($this->user->id);

            $this->response->vars['message'] = 'Success';

        } catch (\Exception $e) {
            $this->exceptionHandler($e);
        }

        return $this->response;
    }

    protected function checkGroupRequired(String $route_name): bool
    {
        $field = 'group_id';

        if ($this->request->data == null) {
            return false;
        }

        if ($route_name === 'GroupCreate') {
            $field = 'group_name';
        }

        if ($route_name === 'GroupJoin') {
            $field = 'group_code';
        }

        if (!array_key_exists($field, $this->request->data)) {
            return false;
        }

        return true;
    }
}