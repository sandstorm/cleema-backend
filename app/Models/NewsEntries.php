<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property int      $id
 * @property int      $created_by_id
 * @property int      $updated_by_id
 * @property int      $views
 * @property DateTime $created_at
 * @property DateTime $published_at
 * @property DateTime $updated_at
 * @property Date     $date
 * @property string   $description
 * @property string   $locale
 * @property string   $teaser
 * @property string   $title
 * @property string   $type
 * @property string   $uuid
 */
class NewsEntries extends Model
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
    protected $table = 'news_entries';

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
        'created_at', 'date', 'description', 'locale', 'published_at', 'teaser', 'title', 'type', 'updated_at', 'views',
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
        'id' => 'int', 'created_at' => 'datetime', 'created_by_id' => 'int', 'date' => 'date', 'description' => 'string', 'locale' => 'string', 'published_at' => 'datetime', 'teaser' => 'string', 'title' => 'string', 'type' => 'string', 'updated_at' => 'datetime', 'updated_by_id' => 'int', 'uuid' => 'string', 'views' => 'int'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'date', 'published_at', 'updated_at'
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var boolean
     */
    public $timestamps = true;

    public function region()
    {
        return $this->belongsTo(Regions::class, 'region_id', 'id');
    }

    public function tags()
    {
        return $this->belongsToMany(NewsTags::class, 'news_entries_tags_links', 'news_entry_id', 'news_tag_id');
    }

    public function usersRead()
    {
        return $this->belongsToMany(UpUsers::class, 'news_entries_users_read_links', 'news_entry_id', 'user_id');
    }

    public function usersFavorited()
    {
        return $this->belongsToMany(UpUsers::class, 'up_users_favorited_news_links', 'news_entry_id', 'user_id');
    }

    public function image()
    {
        return $this->belongsTo(Files::class, 'image_id', 'id');
    }
}
