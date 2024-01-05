<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'created_at', 'created_by_id', 'date', 'description', 'locale', 'published_at', 'teaser', 'title', 'type', 'updated_at', 'updated_by_id', 'uuid', 'views'
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
    public $timestamps = false;

    // Scopes...

    // Functions ...

    // Relations ...
}
