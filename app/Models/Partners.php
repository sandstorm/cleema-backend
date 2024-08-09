<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * @property int      $id
 * @property int      $created_by_id
 * @property int      $updated_by_id
 * @property DateTime $created_at
 * @property DateTime $published_at
 * @property DateTime $updated_at
 * @property string   $description
 * @property string   $title
 * @property string   $url
 * @property string   $uuid
 */
class Partners extends Model
{
    use HasFactory;

    public static function boot(): void
    {
        parent::boot();

        self::creating(function ($newsEntry) {
            $newsEntry->uuid = Str::uuid();
            $newsEntry->created_by_id = auth()->id();
        });

        self::saving(function ($newsEntry) {
            $newsEntry->updated_by_id = auth()->id();
        });
    }

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'partners';

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
        'created_at', 'created_by_id', 'description', 'published_at', 'title', 'updated_at', 'updated_by_id', 'url', 'uuid'
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
        'id' => 'int', 'created_at' => 'datetime', 'created_by_id' => 'int', 'description' => 'string', 'published_at' => 'datetime', 'title' => 'string', 'updated_at' => 'datetime', 'updated_by_id' => 'int', 'url' => 'string', 'uuid' => 'string'
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
     * define relation between partners and their challenges
     * @return HasMany
     */
    public function challenges ()
    {
        return $this->hasMany(Challenges::class, 'partner_id', 'id');
    }

    public function projects ()
    {
        return $this->hasMany(Projects::class, 'partner_id', 'id');
    }

    public function challenge_templates()
    {
        return $this->hasMany(ChallengeTemplates::class, 'partner_id', 'id');
    }

    public function logo ()
    {
        return $this->belongsTo(Files::class, 'logo_id', 'id');
    }
}
