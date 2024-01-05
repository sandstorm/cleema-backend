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
 */
class Challenges extends Model
{
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
        'created_at', 'created_by_id', 'description', 'end_date', 'goal_type', 'interval', 'is_public', 'kind', 'locale', 'published_at', 'start_date', 'teaser_text', 'title', 'trophy_processed', 'updated_at', 'updated_by_id', 'uuid', 'views'
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
        'id' => 'int', 'created_at' => 'datetime', 'created_by_id' => 'int', 'description' => 'string', 'end_date' => 'date', 'goal_type' => 'string', 'interval' => 'string', 'is_public' => 'boolean', 'kind' => 'string', 'locale' => 'string', 'published_at' => 'datetime', 'start_date' => 'date', 'teaser_text' => 'string', 'title' => 'string', 'trophy_processed' => 'boolean', 'updated_at' => 'datetime', 'updated_by_id' => 'int', 'uuid' => 'string', 'views' => 'int'
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
    public $timestamps = false;

    // Scopes...

    // Functions ...

    // Relations ...
}
