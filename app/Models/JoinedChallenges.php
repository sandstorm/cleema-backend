<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\Concerns\Has;

/**
 * @property int      $id
 * @property int      $created_by_id
 * @property int      $updated_by_id
 * @property DateTime $created_at
 * @property DateTime $updated_at
 */
class JoinedChallenges extends Model
{
    use HasFactory;
    public static function boot(): void
    {
        parent::boot();

        self::creating(function ($joined_challenges) {
            $joined_challenges->created_by_id = auth()->id();
        });

        self::saving(function ($joined_challenges) {
            $joined_challenges->updated_by_id = auth()->id();
        });
    }

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'joined_challenges';

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
        'anonymous_user_id', 'created_at', 'created_by_id', 'updated_at', 'updated_by_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'int', 'anonymous_user_id' => 'string', 'created_at' => 'datetime', 'created_by_id' => 'int', 'updated_at' => 'datetime', 'updated_by_id' => 'int'
    ];

    protected $guarded = [
        'created_by_id', 'updated_by_id'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at'
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var boolean
     */
    public $timestamps = true;

    /**
     * a joined challenge has many users which joined it --> one to many
     * @return BelongsTo
     */
    public function user ()
    {
        return $this->belongsTo(UpUsers::class,'user_id', 'id');
    }

    /**
     * each joined Challenge has one Challenge
     * @return BelongsTo
     */
    public function challenge ()
    {
        return $this->belongsTo(Challenges::class,'challenge_id','id');
    }

    /**
     * @return HasMany
     */
    public function answers()
    {
        return $this->hasMany(JoinedChallengesAnswers::class, 'joined_challenge_id', 'id');
    }
}
