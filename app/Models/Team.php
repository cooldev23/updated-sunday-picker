<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Schedule> $schedules
 * @property-read int|null $schedules_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team query()
 * @property int $id
 * @property string $team_key
 * @property string $conference
 * @property string $division
 * @property string $fullname
 * @property int $bye_week
 * @property int $global_team_id
 * @property string $primary_color
 * @property string $secondary_color
 * @property string|null $tertiary_color
 * @property string|null $quaternary_color
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereByeWeek($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereConference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereDivision($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereFullname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereGlobalTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team wherePrimaryColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereQuaternaryColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereSecondaryColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereTeamKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereTertiaryColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereUpdatedAt($value)
 */
class Team extends Model
{
    //TODO: this needs to be re-examined
    /**
     * Each team has many schedules.
     *
     * @return HasMany
     */
    public function schedules(): HasMany
    {
        return $this->hasMany('App\Models\Schedule');
    }
}
