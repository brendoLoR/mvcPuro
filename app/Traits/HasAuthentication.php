<?php

namespace App\Traits;

use App\Model\Model;
use App\Model\User;

trait HasAuthentication
{
    public static function attemptLogin(mixed $email, mixed $password): bool|User
    {
        $password = self::getHash($password);

        $user = new static();

        /** @var User $user */
        if (!$userData = $user
            ->where('email', '=', $email)
            ->where('password', '=', $password)
            ->first()) {
            return false;
        }

        $user = new User($userData);
        $token = self::getHash($user->getAttribute('email') . microtime());
        return $user->update(['token' => $token]) ? $user->setAttribute('token' , $token) : false;

    }

    public static function checkToken(string $token): bool|static
    {
        if (strlen($token) < 64) {
            return false;
        }

        if (!$data = (new static())
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

        $attributes['password'] = self::getHash($attributes['password']);
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