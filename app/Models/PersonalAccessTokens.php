<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $abilities
 * @property string $name
 * @property string $token
 * @property string $tokenable_type
 * @property int    $created_at
 * @property int    $expires_at
 * @property int    $last_used_at
 * @property int    $updated_at
 */
class PersonalAccessTokens extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'personal_access_tokens';

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
        'abilities', 'created_at', 'expires_at', 'last_used_at', 'name', 'token', 'tokenable_id', 'tokenable_type', 'updated_at'
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
        'abilities' => 'string', 'created_at' => 'timestamp', 'expires_at' => 'timestamp', 'last_used_at' => 'timestamp', 'name' => 'string', 'token' => 'string', 'tokenable_type' => 'string', 'updated_at' => 'timestamp'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'expires_at', 'last_used_at', 'updated_at'
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
