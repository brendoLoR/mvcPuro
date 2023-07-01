<?php

namespace App\Services;

use App\Model\Drink;

class RankingService
{
    public function __invoke(\DateTime $finishDate, \DateTime $startDate = null, int $interval = null)
    {
        $drinker = new Drink();

        if ($interval) {
            $startDate = (clone $finishDate)->sub(new \DateInterval("P{$interval}D"));
        }

        if (!$ranking = $drinker->select(['sum(drink) as drinks', 'users.name'])
            ->join('users', 'inner', '', 'users.id = drinks.user_id')
            ->where('drinks.created_at', '>=', $startDate->format('Y-m-d H:i:s'))
            ->where('drinks.created_at', '<=', $finishDate->format('Y-m-d H:i:s'))
            ->group(['users.name'])
            ->order('drinks', 'desc')
            ->get()) {

            return false;
        }
        return $ranking;
    }
}