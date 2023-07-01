<?php

namespace App\Services;

use App\Core\Database\DBQuery;
use App\Model\Model;
use App\Model\User;

class UserService
{
    public static function updateUser(array $attributes, User $user): bool
    {
        if (empty($attributes['password'])) {
            unset($attributes['password']);
        } else{
            $attributes['password'] = User::getHash($attributes['password']);
        }

        if (!$user->update($attributes)) {
            return false;
        }
        return true;
    }

    public static function createUser(array $attributes): DBQuery|Model|bool
    {
        return User::create($attributes);
    }

    public static function deleteUserById($user_id): DBQuery|bool
    {
        return (new User())->delete($user_id);
    }
}