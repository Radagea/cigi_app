<?php

namespace CigiApp\Web\Models;

use Majframe\Db\Connector;
use Majframe\Db\Model;

class User extends Model
{
    public int $id;
    public string $email;
    public string $password;
    public string $full_name;
    public string $date_add;
    public static string $table_name = 'users';

    public static function getUsersByGroup(int $id_group)
    {
        $query = 'SELECT * FROM '
            . self::$table_name . ' INNER JOIN ' . User2Group::$table_name
            . ' ON ' . self::$table_name . '.id=' . User2Group::$table_name . '.id_user' .
            ' WHERE ' . User2Group::$table_name . '.id_group = ?';

        $stmt = Connector::getConnector()->getPDO()->prepare($query);
        $stmt->execute([$id_group]);

        $rows = $stmt->fetchAll();

        $users = [];

        foreach ($rows as $row) {
            $user = [
                'id' => $row['id'],
                'email' => $row['email'],
                'full_name' => $row['full_name']
            ];

            $users[] = $user;
        }

        return $users;
    }

    public function getReturnableArray(): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'email' => $this->email,
        ];
    }

    protected static function dbFieldAssignment(): array
    {
        return [
            'id' => [
                'model' => 'id',
                'type' => 'int',
                'key' => true,
            ],
            'email' => [
                'model' => 'email',
                'type' => 'varchar',
                'required' => true,
            ],
            'password' => [
                'model' => 'password',
                'type' => 'varchar',
                'required' => true
            ],
            'full_name' => [
                'model' => 'full_name',
                'type' => 'varchar',
                'required' => true
            ],
            'date_add' => [
                'model' => 'date_add',
                'type' => 'timestamp',
            ]
        ];
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }
}