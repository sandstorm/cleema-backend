<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $title
 * @property string $kind
 * @property DateTime $created_at
 * @property DateTime $published_at
 * @property DateTime $updated_at
 * @property int $created_by_id
 * @property int $updated_by_id
 * @property string $locale
 * @property int $amount
 * @property string $uuid
 * @property int $challenge_id
 * @property int $image_id
 */
class Trophies extends Model
{
    use HasFactory;

    public static function boot(): void
    {
        parent::boot();

        self::creating(function ($trophies) {
            $trophies->uuid = Str::uuid();
            $trophies->created_by_id = auth()->id();
        });

        self::saving(function ($trophies) {
            $trophies->updated_by_id = auth()->id();
        });
    }

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'kind', 'created_at', 'updated_at', 'published_at', 'created_by_id', 'updated_id', 'locale', 'amount', 'uuid'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    protected $guarded = [
        'id', 'uuid', 'created_by_id', 'updated_by_id',
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
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'int',
        'title' => 'string',
        'created_at' => 'datetime',
        'created_by_id' => 'int',
        'published_at' => 'datetime',
        'updated_at' => 'datetime',
        'updated_by_id' => 'int',
        'kind' => 'string',
        'locale' => 'string',
        'amount' => 'int',
        'uuid' => 'string'
    ];

    /**
     * relation to challenges
     * @return BelongsTo
     */
    public function challenge()
    {
        return $this->belongsTo(Challenges::class, 'challenge_id', 'id');
    }

    public function upUsers()
    {
        return $this->belongsToMany(UpUsers::class, 'user_trophies_v2', 'trophy_id', 'user_id');
    }

    public function image()
    {
        return $this->belongsTo(Files::class, 'image_id', 'id');
    }

}
