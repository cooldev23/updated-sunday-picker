<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read \App\Models\User|null $creator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Otp> $otps
 * @property-read int|null $otps_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Pick> $picks
 * @property-read int|null $picks_count
 * @property-read \App\Models\LeagueType|null $type
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|League newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|League newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|League query()
 * @method bool canShowGames()
 * @method void calculateWinPercentages(int $weekNum)
 * @method int getStandardWeekWinner(int $week)
 * @method int getWeightedWeekWinner(int $week)
 * @method array rekeyCorrectPicks(object $leagueUserPicks)
 * @method string getWeightedWeekWinner(array $userIdsWithMaxCorrect)
 * @property int $id
 * @property string $name
 * @property string|null $motto
 * @property int $league_creator_id
 * @property int $league_type_id
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|League whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|League whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|League whereLeagueCreatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|League whereLeagueTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|League whereMotto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|League whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|League whereUpdatedAt($value)
 */
class League extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'motto', 'league_creator_id', 'league_type_id'
    ];

    /**
     * A league can have many users.
     *
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'league_user', 'league_id', 'user_id')->withPivot('survivor_eliminated');
    }

    /**
     * A league has many picks.
     *
     * @return HasMany
     */
    public function picks(): HasMany
    {
        return $this->hasMany(Pick::class, 'league_id_FK');
    }

    /**
     * A league has one creator.
     *
     * @return BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'league_creator_id', 'id');
    }

    /**
     * A league has one type.
     *
     * @return BelongsTo
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(LeagueType::class, 'id', 'league_type_id');
    }

    /**
     * A league has many otp invites.
     *
     * @return HasMany
     */
    public function otps(): HasMany
    {
        return $this->hasMany(Otp::class);
    }

    /**
     * Can show games or not depending on time.
     *
     * @return boolean
     */
    public function canShowGames()
    {
        $today = Carbon::now()->tz(auth()->user()->timezone);
        switch ($today->englishDayOfWeek) {
            case 'Sunday':
                if ($today->hour > 11) {
                    return true;
                }
                break;
            
            case 'Thursday':
                if ($today->hour > 18) {
                    return true;
                }
                break;
            
            default:
                $nonNflMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug'];
                if (in_array($today->shortEnglishMonth, $nonNflMonths)) {
                    return true;
                }
                break;
        }
        return true;
    }
    
    /**
     * Summary of calculateWinPercentages
     * 
     * @param int $weekNum
     * @return void
     */
    public function calculateWinPercentages(int $weekNum)
    {
        foreach ($this->users as $user) {
            $user->load('stats');
            if (!empty($user->stats)) {
                $correct = count($user->picks);
                $weekGamesCount = count(Week::getGamesCount($weekNum));
                $stats = UserStat::create([
                    'user_id' => auth()->id(),
                    'league_id' => $this->id,
                    'week' => $weekNum,
                    'total_correct_wk' => $correct,
                    'total_games_wk' => $weekGamesCount,
                ]);
                $stats->total_correct_yr += $correct;
                $stats->total_games_yr += $weekGamesCount;
                $stats->save();
            }
        }
    }

    /**
     * Get the winner for each week in a standard league.
     *
     * @param integer $week
     * @return integer
     */
    public function getStandardWeekWinner(int $week) : int
    {
        $leagueUsersAndCorrctPicks = $this->picks()->select('user_id_FK', DB::raw('count(*) as "total wins"'))->where([['nfl_week', $week],['correct', 1]])->groupBy('user_id_FK')->get();
        $rekeyedUsersAndPicks = $this->rekeyCorrectPicks($leagueUsersAndCorrctPicks);
        $maxCorrect = max($rekeyedUsersAndPicks);
        $usersWithMaxCorrect = array_keys($rekeyedUsersAndPicks, $maxCorrect);
        if (count($usersWithMaxCorrect) > 1) {
            return $this->checkTiebreaker($usersWithMaxCorrect);
        }
        return $usersWithMaxCorrect[0];
    }

    /**
     * Get the winner for each week in a weighted league.
     *
     * @param integer $week
     * @return integer
     */
    public function getWeightedWeekWinner(int $week) : int
    {
        $winningPlayer = $this->picks()->select('user_id_FK', DB::raw('SUM(weighted_order) as "total"'))->where([['nfl_week', $week],['correct', 1]])->groupBy('user_id_FK')->orderBy('total', 'desc')->limit(1)->get();
        
        return $winningPlayer[0]->user_id_FK;
    }

    /**
     * Order league players by total wins.
     *
     * @param object $leagueUserPicks
     * @return array
     */
    private function rekeyCorrectPicks(object $leagueUserPicks): array
    {
        $orderedResult = [];
        foreach ($leagueUserPicks as $value) {
            $orderedResult[$value['user_id_FK']] = $value['total wins'];
        }
        
        return $orderedResult;
    }

    /**
     * Undocumented function
     *
     * @param array $userIdsWithMaxCorrect
     * @return string
     */
    private function checkTiebreaker(array $userIdsWithMaxCorrect): string
    {
        $tiebreakDifferences = [];
        $mondayNightGame = Schedule::lastGameOfWeek();
        $mondayNightScoreRecord = Score::where('game_id', $mondayNightGame->global_game_id)->first();
        
        foreach ($userIdsWithMaxCorrect as $value) {
            $user = User::find($value);
            $userTiebreak = $user->picks()->where('game_id', $mondayNightGame->global_game_id)->value('tiebreaker');
            $tiebreakDifferences[$user->id] = abs($userTiebreak - $mondayNightScoreRecord->getTotalPoints());
        }
     
        return array_keys($tiebreakDifferences, min($tiebreakDifferences))[0];
    }
}
