<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * @property int      $id
 * @property int      $created_by_id
 * @property int      $updated_by_id
 * @property DateTime $created_at
 * @property DateTime $updated_at
 * @property string   $title
 * @property string   $uuid
 */
class ChallengeImages extends Model
{
    public static function boot(): void
    {
        parent::boot();

        self::creating(function ($challenges_image) {
            $challenges_image->uuid = Str::uuid();
            $challenges_image->created_by_id = auth()->id();
        });

        self::saving(function ($challenges_image) {
            $challenges_image->updated_by_id = auth()->id();
        });
    }

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'challenge_images';

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
        'created_at', 'created_by_id', 'title', 'updated_at', 'updated_by_id', 'uuid'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'int', 'created_at' => 'datetime', 'created_by_id' => 'int', 'title' => 'string', 'updated_at' => 'datetime', 'updated_by_id' => 'int', 'uuid' => 'string'
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
     * one image can be used by multiple challenges
     * @return HasMany
     */
    public function challenges ()
    {
        return $this->hasMany(Challenges::class, 'image_id', 'image_id');
    }

    public function image ()
    {
        return $this->belongsTo(Files::class, 'image_id', 'id');
    }

    public function challenge_templates ()
    {
        return $this->belongsTo(ChallengeTemplates::class, 'image_id', 'id');
    }
}
