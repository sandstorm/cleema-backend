<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int      $id
 * @property int      $created_by_id
 * @property int      $updated_by_id
 * @property string   $anonymous_user_id
 * @property string   $code
 * @property DateTime $created_at
 * @property DateTime $updated_at
 * @property Date     $redeemed_at
 */
class VoucherRedemptions extends Model
{
    use HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'voucher_redemptions';

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
        'anonymous_user_id', 'code', 'created_at', 'created_by_id', 'redeemed_at', 'updated_at', 'updated_by_id','redeemer_id', 'offer_id'
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
        'id' => 'int', 'anonymous_user_id' => 'string', 'code' => 'string', 'created_at' => 'datetime', 'created_by_id' => 'int', 'redeemed_at' => 'date', 'updated_at' => 'datetime', 'updated_by_id' => 'int','redeemer_id'=>'int', 'offer_id' => 'int'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'redeemed_at', 'updated_at'
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var boolean
     */
    public $timestamps = false;

    public function redeemer ()
    {
        return $this->belongsTo(UpUsers::class, 'redeemer_id', 'id');
    }

    public function offer ()
    {
        return $this->belongsTo(Offers::class, 'offer_id', 'id');
    }
}
