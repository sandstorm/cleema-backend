<?php

namespace App\Models;

use Filament\Actions\Concerns\HasName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int      $id
 * @property int      $created_by_id
 * @property int      $referral_count
 * @property int      $updated_by_id
 * @property boolean  $accepts_surveys
 * @property boolean  $blocked
 * @property boolean  $confirmed
 * @property boolean  $is_supporter
 * @property string   $confirmation_token
 * @property string   $email
 * @property string   $password
 * @property string   $provider
 * @property string   $referral_code
 * @property string   $reset_password_token
 * @property string   $username
 * @property string   $uuid
 * @property DateTime $created_at
 * @property DateTime $updated_at
 */
class UpUsers extends Authenticatable implements \Filament\Models\Contracts\HasName
{
    use HasApiTokens, HasFactory, Notifiable;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'up_users';

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
        'accepts_surveys', 'blocked', 'confirmed', 'created_at', 'created_by_id', 'email', 'is_supporter', 'provider', 'referral_code', 'referral_count', 'reset_password_token', 'updated_at', 'updated_by_id', 'username', 'uuid'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'confirmation_token'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'int', 'accepts_surveys' => 'boolean', 'blocked' => 'boolean', 'confirmation_token' => 'string', 'confirmed' => 'boolean', 'created_at' => 'datetime', 'created_by_id' => 'int', 'email' => 'string', 'is_supporter' => 'boolean', 'password' => 'string', 'provider' => 'string', 'referral_code' => 'string', 'referral_count' => 'int', 'reset_password_token' => 'string', 'updated_at' => 'datetime', 'updated_by_id' => 'int', 'username' => 'string', 'uuid' => 'string'
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

    public function getFilamentName(): string
    {
        return $this->getAttributeValue('username');
    }
}
