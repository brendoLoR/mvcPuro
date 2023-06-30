<?php

namespace App\Model;

use App\Traits\HasAuthentication;

class User extends Model
{
    use HasAuthentication;
    protected string $table = 'users';

    protected static array $hidden = ['password', 'token'];

}