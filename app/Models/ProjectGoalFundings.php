<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $current_amount
 * @property int $total_amount
 */
class ProjectGoalFundings extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'projects_goal_fundings_v2';

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
        'current_amount', 'total_amount'
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
        'id' => 'int', 'current_amount' => 'int', 'total_amount' => 'int'
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

    public function project ()
    {
        return $this->hasMany(Projects::class, 'goal_funding_id', 'id');
    }
}
