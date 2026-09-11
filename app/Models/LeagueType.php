<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read \App\Models\League|null $league
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeagueType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeagueType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeagueType query()
 * @property int $id
 * @property string $name
 * @property string $description
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\League> $leagues
 * @property-read int|null $leagues_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeagueType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeagueType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeagueType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeagueType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeagueType whereUpdatedAt($value)
 */
class LeagueType extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'league_types';

    /**
     * Every league has a LeagueType.
     *
     * @return HasMany
     */
    public function leagues(): HasMany
    {
        return $this->hasMany('App\Models\League', 'league_type_id', 'id');
    }
}
