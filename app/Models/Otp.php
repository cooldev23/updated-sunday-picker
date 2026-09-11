<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read \App\Models\League|null $league
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Otp newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Otp newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Otp query()
 * @method bool generate(string $invitees, League $league, User $user, int $length = 32)
 * @method Otp validate(string $code, League $league, string $email)
 * @property int $id
 * @property string $email_address
 * @property string $code
 * @property string $expiration
 * @property int $league_id
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Otp whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Otp whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Otp whereEmailAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Otp whereExpiration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Otp whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Otp whereLeagueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Otp whereUpdatedAt($value)
 */
class Otp extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email_address', 'code', 'expiration', 'league_id'
    ];

    /**
     * Each OTP belongs to a league.
     *
     * @return BelongsTo
     */
    public function league(): BelongsTo
    {
        return $this->belongsTo('App\Models\League');
    }

    /**
     * Undocumented function
     *
     * @param string $invitees
     * @param League $league
     * @param User $user
     * @param integer $length
     * @return boolean
     */
    public static function generate(string $invitees, League $league, User $user, int $length = 32): bool
    {
        $leagueMembers = explode(', ', trim($invitees));
            foreach ($leagueMembers as $address) {
                // assign each address a code and store code, league_id, email address of invitee and expiry date
                $code = substr(md5(microtime()),0,$length);
                $otp = Otp::create([
                    'code' => $code,
                    'email_address' => $address,
                    'expiration' => Carbon::now()->addDay(),
                    'league_id' => $league->id     
                ]);
                // send email to email address with link containing league_id and code
                try {
                    Mail::to($address)->send(new EmailInvite($league, $user, $code, $address));
                } catch (\Exception $e) {
                    // this needs to be refactored
                    return false;
                }
                
            }
        return true;
    }

    /**
     * Validate the provided otp is not expired.
     *
     * @param string $code
     * @param League $league
     * @param string $email
     * @return Otp
     */
    public static function validate(string $code, League $league, string $email): Otp
    {
        $validatedOtp = Otp::where([
            ['code', $code],
            ['league_id', $league->id],
            ['email_address', $email],
        ])->whereDate('expiration', '>=', Carbon::now())->first();

        return $validatedOtp;
    }
}
