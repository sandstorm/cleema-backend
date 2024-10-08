<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property int      $id
 * @property int      $created_by_id
 * @property int      $updated_by_id
 * @property DateTime $created_at
 * @property DateTime $published_at
 * @property DateTime $updated_at
 * @property string   $locale
 * @property string   $uuid
 */
class UserAvatars extends Model
{
    use HasFactory;

    public static function boot(): void
    {
        parent::boot();

        self::creating(function ($file) {
            $file->created_by_id = auth()->id();
            $file->uuid = Str::uuid();
        });

        self::saving(function ($file) {
            $file->updated_by_id = auth()->id();
            if ($file->uuid == null) {
                $file->uuid == Str::uuid();
            }
        });
    }

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_avatars';

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
        'created_at', 'created_by_id', 'locale', 'published_at', 'updated_at', 'updated_by_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'int', 'created_at' => 'datetime', 'created_by_id' => 'int', 'locale' => 'string', 'published_at' => 'datetime', 'updated_at' => 'datetime', 'updated_by_id' => 'int', 'uuid' => 'string'
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
    public $timestamps = false;

    public function users ()
    {
        return $this->hasMany(UpUsers::class, 'id', 'avatar_id');
    }

    public function image ()
    {
        return $this->belongsTo(Files::class, 'image_id', 'id');
    }
}
