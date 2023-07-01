<?php

namespace App\Controller;

use App\Core\Http\Response;
use App\Model\Drink;
use App\Model\User;
use App\Services\RankingService;
use DateTime;

class UserDrinkController extends BaseController
{
    /**
     * @throws \Exception
     */
    public function drink($user_id): Response
    {
        if ($this->request()->user()->getAttribute('id') != $user_id) {
            return $this->abort();
        }

        if (!$validated = $this->request()->validate([
            'drink' => ['required', 'instanceOf:' . FILTER_VALIDATE_INT],
        ])) {
            return $this->abort(400, "Request error");
        }

        if (!Drink::create([
            'user_id' => $user_id,
            ...$validated
        ])) {
            return $this->abort(501, "Error on server");
        }

        return $this->response()->status(200)->message("Drink registreded")->json([
            'user' => User::find($user_id)->attributes,
        ]);
    }


    public function history($user_id): Response
    {
        if (($user = $this->request()->user())->getAttribute('id') != $user_id) {
            return $this->abort();
        }

        if (!$drinkHistory = $user->drinks()
            ->select(["DISTINCT DATE(created_at) as `date`", "SUM(drink) as drink_sum", "COUNT(id) as register_count"])
            ->group(["`date`"])->get()) {

            return $this->response()->message("Historic not found")
                ->status(404)->json([]);
        }

        return $this->response()->message("This is your historic")
            ->json(['user' => $user, 'history' => $drinkHistory]);
    }

    public function ranking(int $year, int $month, int $day)
    {
        if (!$date = DateTime::createFromFormat('Y-m-d H:i:s', "$year-$month-$day 00:00:00")) {
            return $this->abort(400, "Invalid date format, expected: Y-m-d, redived: {$date->format('Y-m-d')}");
        }

        if (!$ranking = (new RankingService())((clone $date)->add(new \DateInterval('PT24H')), $date)) {
            return $this->response()->message("Ranking not found")
                ->status(404)->json([]);
        }

        return $this->response()->message("This is the ranking of day: {$date->format('Y-m-d')}")
            ->json(['date' => $date->format('Y-m-d'), 'ranking' => $ranking]);

    }

    public function rankingInterval(int $days)
    {
        if (!$date = new DateTime()) {
            return $this->abort(400, "Invalid date format, expected: Y-m-d, redived: {$date->format('Y-m-d')}");
        }

        if (!$ranking = (new RankingService())($date, interval: $days)) {
            return $this->response()->message("Ranking not found")
                ->status(404)->json([]);
        }

        return $this->response()->message("This is the ranking of day: {$date->format('Y-m-d')}")
            ->json([
                'interval' => $days,
                'finishDate' => $date->format('Y-m-d'),
                'ranking' => $ranking]);

    }

}