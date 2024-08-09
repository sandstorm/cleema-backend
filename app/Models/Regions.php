<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * @property int      $id
 * @property int      $created_by_id
 * @property int      $updated_by_id
 * @property DateTime $created_at
 * @property DateTime $published_at
 * @property DateTime $updated_at
 * @property string   $name
 * @property string   $uuid
 * @property boolean  $is_public
 * @property boolean  $is_supraregional
 */
class Regions extends Model
{
    use HasFactory;

    public static function boot(): void
    {
        parent::boot();

        self::creating(function ($region) {
            $region->created_by_id = auth()->id();
            $region->uuid = Str::uuid();
            if (!$region->is_supraregional) {
                $region->is_supraregional = false;
            }
        });

        self::saving(function ($region) {
            $region->updated_by_id = auth()->id();
            if ($region->uuid == null) {
                $region->uuid = Str::uuid();
            }
        });
    }

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'regions';

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
        'created_at', 'created_by_id', 'name', 'published_at', 'updated_at', 'updated_by_id', 'uuid', 'is_public', 'is_supraregional'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'int', 'created_at' => 'datetime', 'created_by_id' => 'int', 'name' => 'string', 'published_at' => 'datetime', 'updated_at' => 'datetime', 'updated_by_id' => 'int', 'uuid' => 'string', 'is_public' => 'boolean', 'is_supraregional' => 'boolean'
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
     * a region has multiple users within it --> one to many
     * @return HasMany
     */
    public function users ()
    {
        return $this->hasMany(UpUsers::class, 'region_id', 'id');
    }

    /**
     * a region has multiple challenges  --> one to many
     * @return HasMany
     */
    public function challenges ()
    {
        return $this->hasMany(Challenges::class, 'region_id', 'id');
    }

    /**
     * a region has multiple offers --> one to many
     * @return HasMany
     */
    public function offers ()
    {
        return $this->hasMany(Offers::class, 'region_id', 'id');
    }

    public function projects ()
    {
        return $this->hasMany(Projects::class, 'region_id', 'id');
    }

    public function newsEntries ()
    {
        return $this->hasMany(NewsEntries::class, 'region_id', 'id');
    }
}
