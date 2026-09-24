<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Passkeys\Passkey> $passkeys
 * @property-read int|null $passkeys_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorRecoveryCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @property string $first_name
 * @property string $last_name
 * @property string $username
 * @property string|null $timezone
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTimezone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUsername($value)
 */
#[Fillable(['first_name', 'last_name', 'display_name', 'email', 'password'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable, HasRoles;

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'display_name';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    /**
     * Intercept the email attribute and convert it to lowercase before saving.
     */
    protected function email(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => Str::lower($value),
        );
    }

    /**
     * A user can belong to many leagues.
     *
     * @return BelongsToMany
     */
    public function leagues(): BelongsToMany
    {
        return $this->belongsToMany(League::class, 'league_user', 'user_id', 'league_id')->withPivot('survivor_eliminated');
    }

    /**
     * A user will have many picks.
     *
     * @return HasMany
     */
    public function picks(): HasMany
    {
        return $this->hasMany(Pick::class, 'user_id_FK');
    }

    /**
     * A user will have many stats.
     *
     * @return HasMany
     */
    public function stats(): HasMany
    {
        return $this->hasMany(UserStat::class, 'user_id');
    }

    /**
     * A user will have one latest stat.
     *
     * @return HasOne
     */
    public function statsLatest(): HasOne
    {
        return $this->hasOne(UserStat::class)->latestOfMany();
    }

    /**
     * Summary of setCorrectPicksAndPercentages
     * @param int $cw
     * @return void
     */
    public function setCorrectPicksAndPercentages(int $cw): void
    {
        // TODO run through week 3
        // TODO add scopes
        $weekWinners = Score::where('nfl_week', $cw)->get(['winner', 'game_id']);
        $formattedWinners = $this->formatWinners($weekWinners);
        $weekPicks = $this->picks()->where('nfl_week', $cw)->get();
        foreach($this->leagues as $league) {
            foreach ($weekPicks as $pick) {
                if ($league->league_type_id === 2) {
                    if ($formattedWinners[$pick->game_id] !== $pick->winner) {
                        $this->leagues()->updateExistingPivot($league->id, ['survivor_eliminated' => 1]);
                    }
                }
                if ($formattedWinners[$pick->game_id] === $pick->winner || $formattedWinners[$pick->game_id] === 'tie') {
                    $pick->correct = true;
                    $pick->save();
                }
            }

            $numCorrect = Pick::where([['user_id_FK', $this->id], ['league_id_FK', $league->id], ['nfl_week', $cw], ['correct', 1]])->count();
            
            $numGamesThisWeek = Schedule::where([['nfl_week', $cw], ['away_team', '!=', 'BYE']])->count();

            $stat = UserStat::updateOrCreate(
                [
                    'user_id' => $this->id,
                    'league_id' => $league->id
                ],
                [
                    'week' => $cw,
                    'total_correct_wk' => $numCorrect,
                    'total_games_wk' => $numGamesThisWeek
                ]
            );

            $pastWeeks = [];
            for ($i=$cw; $i > 0; $i--) { 
                array_push($pastWeeks, $i);
            }

            $stat->total_correct_yr = $this->picks()->where([['league_id_FK', $league->id], ['correct', 1]])->count();
            $stat->total_games_yr = Schedule::where([['away_team', '!=', 'BYE']])->whereIn('nfl_week', $pastWeeks)->count();
            $stat->save();
        }
    }

    /**
     * Create array with game id as keys
     *
     * @param object $winners
     * @return array
     */
    private function formatWinners(object $winners): array
    {
        $tempArr = [];
        foreach ($winners as $winner) {
            $tempArr[$winner->game_id] = $winner->winner;
        }
        return $tempArr;
    }

    /**
     * Return average weekly correct if stats exist
     *
     * @return float|string
     */
    public function getAvgWeeklyCorrect()
    {
        $totalCorrect = 0;
        if ($this->statsLatest) {
            foreach ($this->stats as $stat) {
                $weekCorrect = ($stat->total_correct_wk/$stat->total_games_wk)  * 100;
                $totalCorrect += $weekCorrect;
            }
            $avgPercentCorrectPerWeek = $totalCorrect / $this->stats()->count();

            return $avgPercentCorrectPerWeek;
        }
        return 'No stats yet';
    }

    public function getYearPercentCorrect()
    {
        if ($this->statsLatest) {
            return ($this->picks()->where('correct', 1)->count()/$this->statsLatest->total_games_yr) * 100;
        }

        return 'No stats yet';
    }

    public function getWeeklyCorrect()
    {
        return $this->statsLatest->total_correct_wk ?? 'No stats yet';
    }

    public function getWeeklyAverage(int $totalGamesThisWeek)
    {
        if ($this->statsLatest) {
            return ($this->statsLatest->total_correct_wk / $totalGamesThisWeek) * 100;
        }
        return 'No stats yet';
    }

    public function getWeekWeightTotal(int $cw, League $league)
    {
        return $this->picks()->where([
            ['nfl_week', $cw],
            ['league_id_FK', $league->id],
            ['correct', 1]
        ])->sum('weighted_order');
    }
}
