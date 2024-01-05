<?php

namespace App\Models\AdminModels;

use App\Models\DateTime;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int      $id
 * @property int      $created_by_id
 * @property int      $updated_by_id
 * @property string   $action
 * @property string   $conditions
 * @property string   $properties
 * @property string   $subject
 * @property DateTime $created_at
 * @property DateTime $updated_at
 */
class AdminPermissions extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'admin_permissions';

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
        'action', 'conditions', 'created_at', 'created_by_id', 'properties', 'subject', 'updated_at', 'updated_by_id'
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
        'id' => 'int', 'action' => 'string', 'conditions' => 'string', 'created_at' => 'datetime', 'created_by_id' => 'int', 'properties' => 'string', 'subject' => 'string', 'updated_at' => 'datetime', 'updated_by_id' => 'int'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at'
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
