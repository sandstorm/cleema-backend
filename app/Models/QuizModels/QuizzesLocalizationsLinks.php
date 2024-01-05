<?php

namespace App\Models\QuizModels;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $inv_quiz_id
 * @property int $quiz_id
 * @property int $quiz_order
 */
class QuizzesLocalizationsLinks extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'quizzes_localizations_links';

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
        'inv_quiz_id', 'quiz_id', 'quiz_order'
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
        'id' => 'int', 'inv_quiz_id' => 'int', 'quiz_id' => 'int', 'quiz_order' => 'int'
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
