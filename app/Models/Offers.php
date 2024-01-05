<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int      $id
 * @property int      $created_by_id
 * @property int      $redeem_interval
 * @property int      $updated_by_id
 * @property int      $views
 * @property DateTime $created_at
 * @property DateTime $published_at
 * @property DateTime $updated_at
 * @property string   $description
 * @property string   $generic_voucher
 * @property string   $individual_vouchers
 * @property string   $locale
 * @property string   $store_type
 * @property string   $summary
 * @property string   $title
 * @property string   $uuid
 * @property boolean  $is_regional
 * @property Date     $valid_from
 * @property Date     $valid_until
 */
class Offers extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'offers';

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
        'created_at', 'created_by_id', 'description', 'discount', 'generic_voucher', 'individual_vouchers', 'is_regional', 'locale', 'published_at', 'redeem_interval', 'store_type', 'summary', 'title', 'updated_at', 'updated_by_id', 'uuid', 'valid_from', 'valid_until', 'views'
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
        'id' => 'int', 'created_at' => 'datetime', 'created_by_id' => 'int', 'description' => 'string', 'generic_voucher' => 'string', 'individual_vouchers' => 'string', 'is_regional' => 'boolean', 'locale' => 'string', 'published_at' => 'datetime', 'redeem_interval' => 'int', 'store_type' => 'string', 'summary' => 'string', 'title' => 'string', 'updated_at' => 'datetime', 'updated_by_id' => 'int', 'uuid' => 'string', 'valid_from' => 'date', 'valid_until' => 'date', 'views' => 'int'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'published_at', 'updated_at', 'valid_from', 'valid_until'
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
