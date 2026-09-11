<?php 

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;

/**
 * This class is intended as a wrapper for the Sportsdata API
 */
class Sportsdata 
{
    /**
     * Class constructor, takes in Guzzle client
     *
     * @param Client $client
     * @param integer $currentWeek
     * @param string $key
     *
     */
    public function __construct(
        private Client $client,
        private int $currentWeek,
        private string $key,
    ) {

    }

    /**
     * Get the current NFL week of the current NFL season.
     *
     * @return JsonResponse|string
     */
    public function getCurrentWeek(): JsonResponse|string
    {
        try {
            $res = $this->client->request('GET', 'scores/json/CurrentWeek' . config('services.sportsdata.key'));
            return json_decode($res->getBody());
        } catch (\Exception $e) {
            // ADD EMAIL TO ME IF THIS HAPPENS
            return 'An error has occurred: ' . $e->getMessage();
        }
    }

    
     /**
     * Get the current NFL season.
     *
     * @return JsonResponse|string
     */
    public function getCurrentSeason()
    {
        try {
            $res = $this->client->request('GET', 'scores/json/CurrentSeason' . config('services.sportsdata.key'));
            return json_decode($res->getBody());
        } catch (\Exception $e) {
            // ADD EMAIL TO ME IF THIS HAPPENS
            return 'An error has occurred: ' . $e->getMessage();
        }
    }


    /**
     * Get the current NFL week of the current NFL season.
     *
     * @return array|string
     */
    public function getSchedule(): array|string
    {
        $season = $this->getCurrentSeason();
        try {
            $res = $this->client->request('GET', 'scores/json/Schedules/'. $season . config('services.sportsdata.key'));
            return json_decode($res->getBody());
        } catch (\Exception $e) {
            return 'An error has occurred: ' . $e->getMessage();
        }
    }

    /**
     * Get active NFL teams information.
     *
     * @return JsonResponse|string
     */
    public function getTeams(): JsonResponse|string
    {
        try {
            $res = $this->client->request('GET', 'scores/json/Teams' . config('services.sportsdata.key'));
            return json_decode($res->getBody());
        } catch (\Exception $e) {
            return 'An error has occurred: ' . $e->getMessage();
        }
    }
    
    /**
     * Get the scores from the current week's games.
     * @param int $season
     * 
     * @return array|string
     */
    public function getScoresByWeek(int $season): array|string
    {
        try {
            $res = $this->client->request('GET', 'scores/json/ScoresByWeek/' . $season . '/' . $this->currentWeek . config('services.sportsdata.key'));
            
            return json_decode($res->getBody());
        } catch (\Exception $e) {
            return 'An error has occurred: ' . $e->getMessage();
        }
    }

    /**
     * Get score by game ID.
     * @param int $gameId
     * 
     * @return JsonResponse|string
     */
    public function getMondayNightScore(int $gameId): JsonResponse|string
    {
        try {
            $res = $this->client->request('GET', 'stats/json/BoxScoreByScoreIDV3/' . $gameId .  config('services.sportsdata.key'));
            return json_decode($res->getBody());
        } catch (\Exception $e) {
            return 'An error has occurred: ' . $e->getMessage();
        }
    }
}