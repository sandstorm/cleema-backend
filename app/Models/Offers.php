<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

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
 * @property string   $url
 * @property int      $region_id
 */
class Offers extends Model
{
    use HasFactory;

    public static function boot(): void
    {
        parent::boot();

        self::creating(function ($offers) {
            $offers->uuid = Str::uuid();
            $offers->created_by_id = auth()->id();
        });

        self::saving(function ($offers) {
            $offers->updated_by_id = auth()->id();
        });
    }

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
        'created_at', 'created_by_id', 'description', 'discount', 'generic_voucher', 'individual_vouchers',
        'is_regional', 'locale', 'published_at', 'redeem_interval', 'store_type', 'summary', 'title',
        'updated_at', 'updated_by_id', 'url', 'uuid', 'valid_from', 'valid_until', 'views', 'region_id',
    ];

    protected $guarded = [
        'uuid', 'created_by_id', 'updated_by_id'
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
        'id' => 'int',
        'discount' => 'int',
        'created_at' => 'datetime',
        'created_by_id' => 'int',
        'description' => 'string',
        'generic_voucher' => 'string',
        'individual_vouchers' => 'string',
        'is_regional' => 'boolean',
        'locale' => 'string',
        'published_at' => 'datetime',
        'redeem_interval' => 'int',
        'store_type' => 'string',
        'summary' => 'string',
        'title' => 'string',
        'updated_at' => 'datetime',
        'updated_by_id' => 'int',
        'uuid' => 'string',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'views' => 'int',
        'region_id' => 'int'
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
    public $timestamps = true;

    /**
     * each offer belongs to a region
     * @return BelongsTo
     */
    public function region ()
    {
        return $this->belongsTo(Regions::class, 'region_id', 'id');
    }

    public function voucherRedemptions ()
    {
        return $this->hasMany(VoucherRedemptions::class, 'offer_id', 'id');
    }

    public function location ()
    {
        return $this->belongsTo(Locations::class, 'location_id', 'id');
    }

    public function address ()
    {
        return $this->belongsTo(Addresses::class, 'address_id', 'id');
    }

    public function image ()
    {
        return $this->belongsTo(Files::class, 'image_id', 'id');
    }

}
