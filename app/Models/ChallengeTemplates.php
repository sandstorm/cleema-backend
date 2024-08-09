<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

/**
 * @property int      $id
 * @property int      $created_by_id
 * @property int      $updated_by_id
 * @property DateTime $created_at
 * @property DateTime $published_at
 * @property DateTime $updated_at
 * @property string   $description
 * @property string   $goal_type
 * @property string   $interval
 * @property string   $kind
 * @property string   $teaser_text
 * @property string   $title
 * @property boolean  $is_public
 */
class ChallengeTemplates extends Model
{
    use HasFactory;

    public static function boot(): void
    {
        parent::boot();

        self::creating(function ($challengeTemplates) {
            $challengeTemplates->created_by_id = auth()->id();
        });

        self::saving(function ($challengeTemplates) {
            $challengeTemplates->updated_by_id = auth()->id();
        });
    }

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'challenge_templates';

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
        'created_at', 'created_by_id', 'description', 'goal_type', 'interval', 'is_public', 'kind', 'published_at', 'teaser_text', 'title', 'updated_at', 'updated_by_id'
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
        'id' => 'int', 'created_at' => 'datetime', 'created_by_id' => 'int', 'description' => 'string', 'goal_type' => 'string', 'interval' => 'string', 'is_public' => 'boolean', 'kind' => 'string', 'published_at' => 'datetime', 'teaser_text' => 'string', 'title' => 'string', 'updated_at' => 'datetime', 'updated_by_id' => 'int'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'published_at', 'updated_at'
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var boolean
     */
    public $timestamps = true;

    /**
     * partner relation
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function partner()
    {
        return $this->belongsTo(Partners::class, 'partner_id', 'id');
    }

    /**
     * relation to goalType steps
     * @return HasOne
     */
    public function goalTypeSteps ()
    {
        return $this->hasOne(ChallengeGoalTypeSteps::class, 'challenge_template_id', 'id');
    }

    /**
     * relation to goalType measurement
     * @return HasOne
     */
    public function goalTypeMeasurement ()
    {
        return $this->hasOne(ChallengeGoalTypeMeasurements::class, 'challenge_template_id', 'id');
    }

    public function image ()
    {
        //return $this->belongsTo(ChallengeImages::class, 'image_id', 'id');
        return $this->belongsTo(Files::class, 'image_id', 'id');
    }
}
