<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int      $id
 * @property int      $created_by_id
 * @property int      $updated_by_id
 * @property string   $conclusion
 * @property string   $description
 * @property string   $goal_type
 * @property string   $locale
 * @property string   $phase
 * @property string   $summary
 * @property string   $title
 * @property string   $uuid
 * @property DateTime $created_at
 * @property DateTime $published_at
 * @property DateTime $start_date
 * @property DateTime $updated_at
 * @property boolean  $trophy_processed
 */
class Projects extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'projects';

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
        'conclusion', 'created_at', 'created_by_id', 'description', 'goal_type', 'locale', 'phase', 'published_at', 'start_date', 'summary', 'title', 'trophy_processed', 'updated_at', 'updated_by_id', 'uuid'
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
        'id' => 'int', 'conclusion' => 'string', 'created_at' => 'datetime', 'created_by_id' => 'int', 'description' => 'string', 'goal_type' => 'string', 'locale' => 'string', 'phase' => 'string', 'published_at' => 'datetime', 'start_date' => 'datetime', 'summary' => 'string', 'title' => 'string', 'trophy_processed' => 'boolean', 'updated_at' => 'datetime', 'updated_by_id' => 'int', 'uuid' => 'string'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'published_at', 'start_date', 'updated_at'
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
