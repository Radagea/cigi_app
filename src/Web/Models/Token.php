<?php

namespace CigiApp\Web\Models;

use Majframe\Db\Model;

class Token extends Model
{
    public int $id;
    public int $id_user;
    public String $token;
    public bool $is_active;
    public String $expiration_date;
    protected static String $table_name = 'Tokens';

    private static function generateToken(int $id_user, int $exp = 2) : bool|Token
    {
        $token = new Token();
        $token->id_user = $id_user;
        $token->token = base64_encode('CigiApp-' . time() . '-' . uniqid());
        $token->is_active = true;
        $token->expiration_date = date('Y-m-d H:i:s', (time() + (2 * 60 * 60)));

        if (!$token->save()) {
            return false;
        }

        return $token;
    }

    public static function getToken(User $user) : Token|bool
    {
        /** @var Token $token */
        $token = Token::get([
            [
                'field' => 'id_user',
                'value' => $user->id
            ],
            [
                'field' => 'is_active',
                'value' => '1',
                'prev_rel' => 'AND'
            ]
        ]);

        if (!$token) {
            return static::generateToken($user->id);
        }

        $exp_date = strtotime($token->expiration_date);

        if($exp_date < (time() + (10*60))) {
            $token->turnOffToken();
            return static::generateToken($user->id);
        }

        return $token;
    }

    public function checkToken() : bool
    {
        if (!$this->is_active) {
            return false;
        }

        if  (strtotime($this->expiration_date) < (time() + (10 * 60))) {
            $this->turnOffToken();
            $token = static::generateToken($this->id_user);

            $this->id = $token->id;
            $this->token = $token->token;
            $this->is_active = $this->is_active;
            $this->expiration_date = $this->expiration_date;
        }

        return true;
    }

    private function turnOffToken() {
        $this->is_active = false;
        $this->save();
    }

    protected static function dbFieldAssignment(): array
    {
        return [
            'id' => [
                'model' => 'id',
                'type' => 'int',
                'key' => true,
            ],
            'id_user' => [
                'model' => 'id_user',
                'type'  => 'int',
            ],
            'token' => [
                'model' => 'token',
                'type'  => 'varchar',
            ],
            'is_active' => [
                'model' => 'is_active',
                'type'  => 'tinyint',
            ],
            'expiration_date' => [
                'model' => 'expiration_date',
                'type'  => 'timestamp'
            ]
        ];
    }
}