<?php

namespace App\Models\ComponentsModels;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $id
 * @property int    $day_index
 * @property string $answer
 */
class ComponentsChallengeAnswers extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'components_challenge_answers';

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
        'answer', 'day_index'
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
        'id' => 'int', 'answer' => 'string', 'day_index' => 'int'
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

    // Scopes...

    // Functions ...

    // Relations ...
}
