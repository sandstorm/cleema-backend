<?php

namespace App\Models;

use App\Filament\Resources\UpUsersResource\RelationManagers\TrophiesRelationManager;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

/**
 * @property int      $id
 * @property int      $created_by_id
 * @property int      $updated_by_id
 * @property int      $views
 * @property DateTime $created_at
 * @property DateTime $published_at
 * @property DateTime $updated_at
 * @property string   $description
 * @property string   $goal_type
 * @property string   $interval
 * @property string   $kind
 * @property string   $locale
 * @property string   $teaser_text
 * @property string   $title
 * @property string   $uuid
 * @property Date     $end_date
 * @property Date     $start_date
 * @property boolean  $is_public
 * @property boolean  $trophy_processed
 * @property int      $collective_goal_amount
 */
class Challenges extends Model
{
    use HasFactory;
    public static function boot(): void
    {
        parent::boot();

        self::creating(function ($challenges) {
            $challenges->uuid = Str::uuid();
            $challenges->created_by_id = auth()->id();
        });

        self::saving(function ($challenges) {
            $challenges->updated_by_id = auth()->id();
        });
    }

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'challenges';

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
        'collective_goal_amount', 'created_at', 'created_by_id', 'description', 'end_date', 'goal_type', 'interval', 'is_public', 'kind', 'locale', 'published_at', 'start_date', 'teaser_text', 'title', 'trophy_processed', 'updated_at', 'updated_by_id', 'uuid', 'views'
    ];

    protected $guarded = [
        'uuid', 'created_by_id', 'updated_by_id'
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
        'id' => 'int', 'created_at' => 'datetime', 'created_by_id' => 'int', 'description' => 'string', 'end_date' => 'date', 'goal_type' => 'string', 'interval' => 'string', 'is_public' => 'boolean', 'kind' => 'string', 'locale' => 'string', 'published_at' => 'datetime', 'start_date' => 'date', 'teaser_text' => 'string', 'title' => 'string', 'trophy_processed' => 'boolean', 'updated_at' => 'datetime', 'updated_by_id' => 'int', 'uuid' => 'string', 'views' => 'int', 'collective_goal_amount' => 'int'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'end_date', 'published_at', 'start_date', 'updated_at'
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var boolean
     */
    public $timestamps = true;

    /**
     * each challenge has an author
     * @return BelongsTo
     */
    public function author ()
    {
        return $this->belongsTo(UpUsers::class, 'author_id', 'id');
    }

    /**
     * each challenges has one region
     * @return belongsTo
     */
    public function region ()
    {
        return $this->belongsTo(Regions::class, 'region_id', 'id');
    }

    /**
     * each challenge has a image
     * @return BelongsTo
     */
    public function image ()
    {
        // return $this->belongsTo(ChallengeImages::class, 'image_id', 'id');
        return $this->belongsTo(Files::class, 'image_id', 'id');
    }

    /**
     * inverse to challenge - partner relation
     * @return BelongsTo
     */
    public function partner ()
    {
        return $this->belongsTo(Partners::class, 'partner_id', 'id');
    }

    public function joinedUsers (){
        return $this->belongsToMany(UpUsers::class, 'joined_challenges', 'challenge_id', 'user_id');
    }

    // TODO FIX seems weird
    public function trophy ()
    {
        return $this->hasOne(Trophies::class, 'challenge_id', 'id');
    }

    public function goalTypeSteps ()
    {
        return $this->hasOne(ChallengeGoalTypeSteps::class, 'challenge_id', 'id');
    }

    public function goalTypeMeasurement ()
    {
        return $this->hasOne(ChallengeGoalTypeMeasurements::class, 'challenge_id', 'id');
    }

    public function joinedChallenges()
    {
        return $this->hasMany(JoinedChallenges::class, 'challenge_id', 'id');
    }
}
