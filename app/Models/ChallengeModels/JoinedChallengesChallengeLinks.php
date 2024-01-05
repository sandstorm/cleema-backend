<?php

namespace App\Models\ChallengeModels;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $challenge_id
 * @property int $joined_challenge_id
 * @property int $joined_challenge_order
 */
class JoinedChallengesChallengeLinks extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'joined_challenges_challenge_links';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'challenge_id', 'joined_challenge_id', 'joined_challenge_order'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'int', 'challenge_id' => 'int', 'joined_challenge_id' => 'int', 'joined_challenge_order' => 'int'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [

    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var boolean
     */
    public $timestamps = false;

    // Scopes...

    // Functions ...

    // Relations ...
}
