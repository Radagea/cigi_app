<?php

namespace CigiApp\Web\Models;

use Majframe\Db\Connector;
use Majframe\Db\Model;

class User2Group extends Model
{
    public int $id_user;
    public int $id_group;
    public string $date_add;
    public static String $table_name = 'users2groups';

    public static function onGroupDelete($id_group) : void
    {
        Connector::getConnector()->executeD(static::$table_name, [['field' => 'id_group', 'value' => $id_group]]);
    }

    public static function joinGroup(int $id_user, int $id_group) : bool {
        if (User2Group::count([
            [
                'field' => 'id_user',
                'value' => $id_user,
            ],
            [
                'field' => 'id_group',
                'value' => $id_group,
                'prev_rel' => 'AND'
            ]
        ])) {
            return false;
        }

        $u2g = new User2Group();
        $u2g->id_user = $id_user;
        $u2g->id_group = $id_group;
        $u2g->save();

        return true;
    }

    protected static function dbFieldAssignment(): array
    {
        return [
            'id_user' => [
                'type' => 'int',
                'required' => true,
            ],
            'id_group' => [
                'type' => 'int',
                'required' => true,
            ],
            'date_add' => [
                'type' => 'string',
                'required' => false
            ]
        ];
    }
}