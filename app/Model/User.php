<?php

namespace App\Model;

use App\Core\Database\DBQuery;

class User extends Model
{
    protected string $table = 'users';

    protected static array $hidden = ['password'];

    public static function attemptLogin(mixed $email, mixed $password): DBQuery|Model|bool
    {
        $password = hash('sha256', $password);

        $user = new static();

        if (!$user = $user->query()
            ->where('email', '=', $email)
            ->where('password', '=', $password)
            ->first()) {
            return false;
        }

        $user = new User($user);
        $token = hash('sha256', $user->getAttribute('email') . microtime());
        return $user->update(['token' => $token]);
    }

    public static function checkToken(string $token)
    {
        if(strlen($token) < 64){
            return false;
        }

        return (new static())->query()
            ->where('token', '=', $token)
            ->first();
    }

    protected static function beforeSave(Model $model): Model
    {
        if ($password = $model->getAttribute('password')) {
            $model->setAttribute('password', hash('sha256', $password));
        }
        return $model;
    }

}