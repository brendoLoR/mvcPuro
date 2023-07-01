<?php

namespace App\Controller;

use App\Core\Http\Response;
use App\Model\User;
use App\Services\RankingService;
use App\Services\UserDrinkService;
use DateTime;

class UserDrinkController extends BaseController
{
    protected UserDrinkService $drinkService;
    protected RankingService $rankingService;
    public function __construct()
    {
        $this->drinkService = new UserDrinkService();

        $this->rankingService = new RankingService();
    }

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

        if (($this->drinkService)($user_id, $validated)) {

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

        if (!$drinkHistory = $this->drinkService->history($user)) {

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

        if (!$ranking = ($this->rankingService)((clone $date)->add(new \DateInterval('PT24H')), $date)) {
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

        if (!$ranking = ($this->rankingService)($date, interval: $days)) {
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