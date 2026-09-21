<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['league_id', 'invited'])]
class LeagueInvited extends Model
{
    use HasFactory;

    /**
     * The table associated with the model
     *
     * @var string
     */
    protected $table = 'league_invites';

    /**
     * Each invitee was invited for a specific league.
     *
     * @return BelongsTo
     */
    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }
}
