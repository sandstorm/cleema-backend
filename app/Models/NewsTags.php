<?php

namespace App\Models;

use App\Filament\Resources\RegionsResource\RelationManagers\NewsEntriesRelationManager;
use DateTime;
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
 * @property string   $value
 */
class NewsTags extends Model
{
    use HasFactory;
    public static function boot(): void
    {
        parent::boot();

        self::creating(function ($newsEntry) {
            $newsEntry->uuid = Str::uuid();
            $newsEntry->created_by_id = auth()->id();
            if ($newsEntry->locale == null) $newsEntry->locale = "de-DE";
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
    protected $table = 'news_tags';

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
        'created_at', 'created_by_id', 'locale', 'published_at', 'updated_at', 'updated_by_id', 'uuid', 'value'
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
        'id' => 'int', 'created_at' => 'datetime', 'created_by_id' => 'int', 'locale' => 'string', 'published_at' => 'datetime', 'updated_at' => 'datetime', 'updated_by_id' => 'int', 'uuid' => 'string', 'value' => 'string'
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

    public function newsEntries ()
    {
        return $this->belongsToMany(NewsEntries::class, 'news_entries_tags_links', 'news_tag_id', 'news_entry_id');
    }
}
