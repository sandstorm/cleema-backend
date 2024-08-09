<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property int      $id
 * @property int      $created_by_id
 * @property int      $updated_by_id
 * @property string   $conclusion
 * @property string   $description
 * @property string   $goal_type
 * @property string   $locale
 * @property string   $phase
 * @property string   $summary
 * @property string   $title
 * @property string   $uuid
 * @property DateTime $created_at
 * @property DateTime $published_at
 * @property DateTime $start_date
 * @property DateTime $updated_at
 * @property boolean  $trophy_processed
 * @property int      $goal_involvement_id
 */
class Projects extends Model
{
    use HasFactory;

    public static function boot(): void
    {
        parent::boot();

        self::creating(function ($projects) {
            $projects->uuid = Str::uuid();
            $projects->created_by_id = auth()->id();
        });

        self::saving(function ($projects) {
            $projects->updated_by_id = auth()->id();
        });
    }

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'projects';

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
        'conclusion', 'created_at', 'created_by_id', 'description', 'goal_type', 'locale', 'phase', 'published_at', 'start_date', 'summary', 'title', 'trophy_processed', 'updated_at', 'updated_by_id', 'uuid', 'goal_involvement_id'
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
        'id' => 'int', 'conclusion' => 'string', 'created_at' => 'datetime', 'created_by_id' => 'int', 'description' => 'string', 'goal_type' => 'string', 'locale' => 'string', 'phase' => 'string', 'published_at' => 'datetime', 'start_date' => 'datetime', 'summary' => 'string', 'title' => 'string', 'trophy_processed' => 'boolean', 'updated_at' => 'datetime', 'updated_by_id' => 'int', 'uuid' => 'string', 'goal_involvement_id' => 'int'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'published_at', 'start_date', 'updated_at'
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var boolean
     */
    public $timestamps = true;

    public function usersFavorited()
    {
        return $this->belongsToMany(UpUsers::class, 'projects_users_favorited_links', 'project_id', 'user_id');
    }

    public function usersJoined()
    {
        return $this->belongsToMany(UpUsers::class, 'projects_users_joined_links', 'project_id', 'user_id');
    }

    public function relatedProjects()
    {
        return $this->belongsToMany(Projects::class, 'projects_related_projects_links', 'project_id', 'inv_project_id');
    }

    public function region ()
    {
        return $this->belongsTo(Regions::class, 'region_id', 'id');
    }

    public function partner ()
    {
        return $this->belongsTo(Partners::class, 'partner_id', 'id');
    }

    public function location ()
    {
        return $this->belongsTo(Locations::class, 'location_id', 'id');
    }

    public function goalInvolvement ()
    {
        return $this->belongsTo(ProjectsGoalInvolvements::class, 'goal_involvement_id', 'id');
    }

    public function goalFunding ()
    {
        return $this->belongsTo(ProjectGoalFundings::class, 'goal_funding_id', 'id');
    }

    public function image ()
    {
        return $this->belongsTo(Files::class, 'image_id', 'id');
    }

    public function teaserImage ()
    {
        return $this->belongsTo(Files::class, 'teaser_image_id', 'id');
    }
}
