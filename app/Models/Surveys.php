<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int      $id
 * @property int      $created_by_id
 * @property int      $updated_by_id
 * @property DateTime $created_at
 * @property DateTime $published_at
 * @property DateTime $updated_at
 * @property string   $description
 * @property string   $evaluation_url
 * @property string   $locale
 * @property string   $survey_url
 * @property string   $target
 * @property string   $title
 * @property string   $uuid
 * @property boolean  $finished
 * @property boolean  $trophy_processed
 */
class Surveys extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'surveys';

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
        'created_at', 'created_by_id', 'description', 'evaluation_url', 'finished', 'locale', 'published_at', 'survey_url', 'target', 'title', 'trophy_processed', 'updated_at', 'updated_by_id', 'uuid'
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
        'id' => 'int', 'created_at' => 'datetime', 'created_by_id' => 'int', 'description' => 'string', 'evaluation_url' => 'string', 'finished' => 'boolean', 'locale' => 'string', 'published_at' => 'datetime', 'survey_url' => 'string', 'target' => 'string', 'title' => 'string', 'trophy_processed' => 'boolean', 'updated_at' => 'datetime', 'updated_by_id' => 'int', 'uuid' => 'string'
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

    // Scopes...

    // Functions ...

    // Relations ...
}
