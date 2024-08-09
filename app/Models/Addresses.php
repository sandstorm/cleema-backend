<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $id
 * @property string $city
 * @property string $housenumber
 * @property string $street
 * @property string $zip
 */
class Addresses extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'addresses_v2';

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
        'city', 'housenumber', 'street', 'zip'
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
        'id' => 'int', 'city' => 'string', 'housenumber' => 'string', 'street' => 'string', 'zip' => 'string'
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

    public function offers ()
    {
        return $this->hasMany(Offers::class, 'address_id', 'id');
    }
}
