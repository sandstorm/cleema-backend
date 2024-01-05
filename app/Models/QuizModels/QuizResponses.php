<?php

namespace App\Models\QuizModels;

use App\Models\DateTime;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int      $id
 * @property int      $created_by_id
 * @property int      $updated_by_id
 * @property string   $anonymous_user_id
 * @property string   $answer
 * @property string   $uuid
 * @property DateTime $created_at
 * @property DateTime $date
 * @property DateTime $updated_at
 */
class QuizResponses extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'quiz-responses';

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
        'anonymous_user_id', 'answer', 'created_at', 'created_by_id', 'date', 'updated_at', 'updated_by_id', 'uuid'
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
        'id' => 'int', 'anonymous_user_id' => 'string', 'answer' => 'string', 'created_at' => 'datetime', 'created_by_id' => 'int', 'date' => 'datetime', 'updated_at' => 'datetime', 'updated_by_id' => 'int', 'uuid' => 'string'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'date', 'updated_at'
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
