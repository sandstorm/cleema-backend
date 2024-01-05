<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $id
 * @property int    $component_id
 * @property int    $entity_id
 * @property int    $order
 * @property string $component_type
 * @property string $field
 */
class UpUsersComponents extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'up_users_components';

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
        'component_id', 'component_type', 'entity_id', 'field', 'order'
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
        'id' => 'int', 'component_id' => 'int', 'component_type' => 'string', 'entity_id' => 'int', 'field' => 'string', 'order' => 'int'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        
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
