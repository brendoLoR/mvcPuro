<?php

namespace App\Model;

class User extends Model
{
    protected string $table = 'users';

    protected static array $hidden = ['password', 'token'];

    public static function attemptLogin(mixed $email, mixed $password): string|bool
    {
        $password = self::getHash($password);

        $user = new static();

        if (!$user = $user->query()
            ->where('email', '=', $email)
            ->where('password', '=', $password)
            ->first()) {
            return false;
        }

        $user = new User($user);
        $token = self::getHash($user->getAttribute('email') . microtime());
        return $user->update(['token' => $token]) ? $token : false;

    }

    public static function checkToken(string $token)
    {
        if (strlen($token) < 64) {
            return false;
        }

        if (!$data = (new static())->query()
            ->where('token', '=', $token)
            ->first()) {
            return false;
        }

        return new static($data);
    }

    protected static function beforeSave(Model $model): Model
    {
        if ($password = $model->getAttribute('password')) {
            $model->setAttribute('password', self::getHash($password));
        }
        return $model;
    }

    protected static function beforeUpdate(array $attributes): array
    {
        if (empty($attributes['password'])) {
            unset($attributes['password']);
            return $attributes;
        }

        $attributes['password'] = User::getHash($attributes['password']);
        return $attributes;
    }

    /**
     * @param mixed $password
     * @return string
     */
    public static function getHash(mixed $password): string
    {
        return hash('sha256', $password);
    }

}