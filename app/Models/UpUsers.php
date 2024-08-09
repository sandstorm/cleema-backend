<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;

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
class UpUsers extends Authenticatable
{
    use HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'up_users';

    /**
     * The primary key for the model.
     *
     * @var int
     */
    public $primaryKey = 'id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'accepts_surveys', 'blocked', 'confirmed', 'created_at', 'created_by_id', 'email', 'is_supporter', 'provider', 'referral_code', 'referral_count', 'reset_password_token', 'updated_at', 'updated_by_id', 'username', 'uuid', 'password', 'is_anonymous', 'confirmation_token'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'confirmation_token',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'int', 'accepts_surveys' => 'boolean', 'blocked' => 'boolean', 'confirmation_token' => 'string', 'confirmed' => 'boolean', 'created_at' => 'datetime', 'created_by_id' => 'int', 'email' => 'string', 'is_supporter' => 'boolean', 'password' => 'hashed', 'provider' => 'string', 'referral_code' => 'string', 'referral_count' => 'int', 'reset_password_token' => 'string', 'updated_at' => 'datetime', 'updated_by_id' => 'int', 'username' => 'string', 'uuid' => 'string', 'is_anonymous' => 'boolean'
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

    /**
     *  Each User has 0 - x trophies --> many to many relation
     * @return BelongsToMany
     */
    public function follows ()
    {
        return $this->belongsToMany(UpUsers::class, 'user_follows_v2', 'follows_user_id', 'followed_user_id')->withPivot(['is_request', 'uuid']);
    }

    public function followers ()
    {
        return $this->belongsToMany(UpUsers::class, 'user_follows_v2', 'followed_user_id', 'follows_user_id')->withPivot(['is_request', 'uuid']);
    }

    /**
     * Each User has 0 - x trophies --> many to many relation
     * @return BelongsToMany
     */
    public function trophies ()
    {
        return $this->belongsToMany(Trophies::class, 'user_trophies_v2', 'user_id', 'trophy_id')
            ->withPivot('date', 'notified');
    }

    /**
     * each user has 1 avatar --> one to one relation
     * @return BelongsTo
     */
    public function avatar ()
    {
        return $this->belongsTo(UserAvatars::class,'avatar_id', 'id');
    }

    /**
     * each user has or has not 1 streak-link --> one to one relation
     * @return BelongsTo
     */
    public function quizStreak ()
    {
        return $this->belongsTo(QuizStreaks::class,'quiz_streak_id', 'id');
    }

    /**
     * each user has one region --> one to one relation
     * @return BelongsTo
     */
    public function region ()
    {
        return $this->belongsTo(Regions::class,'region_id', 'id');
    }

    /**
     * each user has one role --> one to one relation
     * @return BelongsTo
     */
    public function role ()
    {
        return $this->belongsTo(UpRoles::class,'role_id', 'id');
    }

    /**
     * a user may have authored one or more challenges
     * @return HasMany
     */
    public function authoredChallenges ()
    {
        return $this->hasMany(Challenges::class, 'author_id', 'id');
    }

    /**
     * responses a user gives to quizzes
     * @return BelongsToMany
     */
    public function quizResponses () {
        return $this->belongsToMany(Quizzes::class, 'quiz_responses_v2','user_id', 'quiz_id')->withPivot(['date', 'answer']);
    }

    /**
     * @return BelongsToMany
     */
    public function projectsFavorited()
    {
        return $this->belongsToMany(Projects::class, 'projects_users_favorited_links', 'user_id', 'project_id');
    }

    /**
     * @return BelongsToMany
     */
    public function projectsJoined ()
    {
        return $this->belongsToMany(Projects::class, 'projects_users_joined_links', 'user_id', 'project_id');
    }

    /**
     * @return BelongsToMany
     */
    public function enteredSurveys ()
    {
        return $this->belongsToMany(Surveys::class, 'surveys_participants_links', 'user_id', 'survey_id');
    }

    /**
     * @return BelongsToMany
     */
    public function readNewsEntries ()
    {
        return $this->belongsToMany(NewsEntries::class, 'news_entries_users_read_links', 'user_id', 'news_entry_id');
    }

    /**
     * @return BelongsToMany
     */
    public function favoritedNewsEntries ()
    {
        return $this->belongsToMany(NewsEntries::class, 'up_users_favorited_news_links', 'user_id', 'news_entry_id');
    }

    /**
     * @return HasMany
     */
    public function voucherRedemptions ()
    {
        return $this->hasMany(VoucherRedemptions::class, 'redeemer_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function joinedChallenges ()
    {
        return $this->hasMany(JoinedChallenges::class, 'user_id', 'id');
    }

    /**
     * @return BelongsToMany
     */
    public function challengesJoined ()
    {
        return $this->belongsToMany(Challenges::class, 'joined_challenges','user_id', 'challenge_id');
    }
}
