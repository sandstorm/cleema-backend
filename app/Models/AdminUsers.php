<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int      $id
 * @property int      $created_by_id
 * @property int      $updated_by_id
 * @property boolean  $blocked
 * @property boolean  $is_active
 * @property DateTime $created_at
 * @property DateTime $updated_at
 * @property string   $email
 * @property string   $firstname
 * @property string   $lastname
 * @property string   $password
 * @property string   $prefered_language
 * @property string   $registration_token
 * @property string   $reset_password_token
 * @property string   $username
 */
class AdminUsers extends Authenticatable implements HasName, FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    public static function boot(): void
    {
        parent::boot();

        self::creating(function ($adminUser) {
            $adminUser->created_by_id = auth()->id();
        });

        self::saving(function ($adminUser) {
            $adminUser->updated_by_id = auth()->id();
        });
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // return str_ends_with($this->email, '') && $this->hasVerifiedEmail();
        return true;
    }

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'admin_users';

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
        'blocked', 'created_at', 'created_by_id', 'email', 'firstname', 'is_active', 'lastname', 'prefered_language', 'updated_at', 'updated_by_id', 'username', 'password','reset_password_token','remember_token'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token',
        'password',
        'reset_password_token',
        'registration_token',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'int',
        'blocked' => 'boolean',
        'created_at' => 'datetime',
        'created_by_id' => 'int',
        'email' => 'string',
        'firstname' => 'string',
        'is_active' => 'boolean',
        'lastname' => 'string',
        'password' => 'hashed',
        'prefered_language' => 'string',
        'registration_token' => 'string',
        'reset_password_token' => 'string',
        'updated_at' => 'datetime',
        'updated_by_id' => 'int',
        'username' => 'string'
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
    public $timestamps = true;

    public function getFilamentName(): string
    {
        return $this->firstname.' '.$this->lastname;
    }
}
