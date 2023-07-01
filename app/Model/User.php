<?php

namespace App\Model;

use App\Traits\HasAuthentication;

class User extends Model
{
    use HasAuthentication;

    protected string $table = 'users';

    protected static array $hidden = ['password', 'token'];

    protected Drink $drink;

    public function __construct(?array $attributes = null)
    {
        parent::__construct($attributes);
        $this->drink = new Drink();
    }

    public function drinks(array $attributes = ['*']): \App\Core\Database\DBQuery
    {
        return $this->drink
            ->where('user_id', '=', $this->getAttribute('id'))
            ->select($attributes);
    }

    protected function getWithCountQuery(): null|string
    {
        return "(". $this->drink
            ->where('drinks.user_id', '=', '`users`.`id`')
            ->group(['drinks.user_id'])
            ->select(['sum(drink)'])
            ->query(true) . ") as drinkCounter" ;
    }
}