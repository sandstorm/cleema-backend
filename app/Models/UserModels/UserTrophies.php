<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int      $id
 * @property int      $created_by_id
 * @property int      $updated_by_id
 * @property string   $anonymous_user_id
 * @property DateTime $created_at
 * @property DateTime $date
 * @property DateTime $updated_at
 * @property boolean  $notified
 */
class UserTrophies extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_trophies';

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
        'anonymous_user_id', 'created_at', 'created_by_id', 'date', 'notified', 'updated_at', 'updated_by_id'
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
        'id' => 'int', 'anonymous_user_id' => 'string', 'created_at' => 'datetime', 'created_by_id' => 'int', 'date' => 'datetime', 'notified' => 'boolean', 'updated_at' => 'datetime', 'updated_by_id' => 'int'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'date', 'updated_at'
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
