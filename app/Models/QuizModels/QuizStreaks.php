<?php

namespace App\Models\QuizModels;

use App\Models\DateTime;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int      $id
 * @property int      $correct_answer_streak
 * @property int      $created_by_id
 * @property int      $max_correct_answer_streak
 * @property int      $participation_streak
 * @property int      $updated_by_id
 * @property string   $anonymous_user_id
 * @property DateTime $created_at
 * @property DateTime $updated_at
 */
class QuizStreaks extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'quiz_streaks';

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
        'anonymous_user_id', 'correct_answer_streak', 'created_at', 'created_by_id', 'max_correct_answer_streak', 'participation_streak', 'updated_at', 'updated_by_id'
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
        'id' => 'int', 'anonymous_user_id' => 'string', 'correct_answer_streak' => 'int', 'created_at' => 'datetime', 'created_by_id' => 'int', 'max_correct_answer_streak' => 'int', 'participation_streak' => 'int', 'updated_at' => 'datetime', 'updated_by_id' => 'int'
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
}
