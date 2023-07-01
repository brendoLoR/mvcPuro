<?php

namespace App\Services;

use App\Model\Drink;
use App\Model\User;

class UserDrinkService
{
    public function __invoke($user_id, bool|array $validated)
    {
        return !Drink::create([
            'user_id' => $user_id,
            ...$validated
        ]);
    }

    public function history(User $user): array
    {
        return $user->drinks()
            ->select(["DISTINCT DATE(created_at) as `date`", "SUM(drink) as drink_sum", "COUNT(id) as register_count"])
            ->group(["`date`"])->get();
    }
}