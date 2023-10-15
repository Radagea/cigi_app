<?php

namespace CigiApp\Web\Models;

use Majframe\Db\Connector;
use Majframe\Db\Model;

class Group extends Model
{
    public int $id;
    public string $name;
    public int $id_user_owner;
    public string $group_code;
    public string $date_add;
    public array $users;

    protected static String $table_name = 'groups';

    public function __construct(String $name = null, int $id_user = null)
    {
        if ($name != null && $id_user != null) {
            $this->name = $name;
            $this->id_user_owner = $id_user;
            $this->generateGroup();
            $this->joinGroup($this->id_user_owner);
        }
    }

    public function deleteG() : bool
    {
        $this->delete();

        User2Group::onGroupDelete($this->id);

        return true;
    }
    public function joinGroup(int $user_id) : bool
    {
        return User2Group::joinGroup($user_id, $this->id);
    }

    public function isIn($user_id) : bool
    {
        return User2Group::count(
            [
                [
                    'field' => 'id_user',
                    'value' => $user_id,
                ],
                [
                    'field' => 'id_group',
                    'value' => $this->id,
                    'prev_rel' => 'AND'
                ]
            ]
        );
    }

    public function leave($user_id) : void
    {
        $user2group = new User2Group();
        $user2group->id_group = $this->id;
        $user2group->id_user = $user_id;

        $user2group->delete();
    }

    public function getAsArray(Bool $need_owner = false, Bool $need_users = false) : Array
    {
        $return = [
            'id' => $this->id,
            'group_code' => $this->group_code,
            'group_name' => $this->name,
            'id_owner_user' => $this->id_user_owner,
        ];

        if ($need_owner) {
            $user = new User();
            $user->id = $this->id_user_owner;

            if ($user->getByField('id')) {
                $return['owner_name'] = $user->full_name;
            }
        }

        if ($need_users) {
            $users = User::getUsersByGroup($this->id);
            $return['users'] = $users;
        }


        return $return;
    }

    public static function getUserGroups(int $id_user) : array
    {
        $query = 'SELECT ' . self::$table_name . '.*, ' . User::$table_name . '.full_name FROM ' . self::$table_name .
            ' INNER JOIN ' . User2Group::$table_name . ' ON ' . User2Group::$table_name . '.id_group = ' . self::$table_name . '.id' .
            ' INNER JOIN ' . User::$table_name . ' ON ' . Group::$table_name . '.id_user_owner = ' . User::$table_name . '.id '
            .'WHERE ' .
            User2Group::$table_name . '.id_user = ?';

        $stmt = Connector::getConnector()->getPDO()->prepare($query);
        $stmt->execute([$id_user]);
        $rows = $stmt->fetchAll();

        $groups = [];

        foreach ($rows as $row) {
            $group = [
                'id' => $row['id'],
                'group_code' => $row['group_code'],
                'group_name' => $row['name'],
                'id_owner_user' => $row['id_user_owner'],
                'owner_name' => $row['full_name']
            ];

            $groups[] = $group;
        }

        return $groups;
    }

    private function generateGroup() : void
    {
        if ($this->save()) {
            $this->group_code = strtoupper(bin2hex(random_bytes(2))) . '-' . $this->id + 100;
            $this->save();
        }
    }

    protected static function dbFieldAssignment(): array
    {
        return [
            'id' => [
                'type' => 'int',
                'key' => true
            ],
            'name' => [
                'type' => 'varchar',
                'required' => true,
            ],
            'id_user_owner' => [
                'type' => 'int',
                'required' => true,
            ],
            'group_code' => [
                'type' => 'string',
                'required' => false
            ],
            'date_add' => [
                'type' => 'string',
                'required' => false
            ]
        ];
    }
}