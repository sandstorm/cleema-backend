<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $question
 * @property string $explanation
 * @property string $correct_answer
 * @property string $uuid
 * @property string $locale
 * @property boolean $is_filler
 * @property int $region_id
 */
class QuizQuestions extends Model
{
    use HasFactory;

    public static function boot(): void
    {
        parent::boot();

        self::creating(function ($quiz_question) {
            $quiz_question->uuid = Str::uuid();
        });
    }

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'quiz_questions';

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
        'question',
        'explanation',
        'correct_answer',
        'uuid',
        'locale',
        'is_filler',
        'region_id'
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
        'question' => 'string',
        'explanation' => 'string',
        'correct_answer' => 'string',
        'uuid' => 'string',
        'locale' => 'string',
        'is_filler' => 'boolean',
        'region_id' => 'int'
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

    public function answers()
    {
        return $this->hasMany(QuizAnswers::class, 'quiz_question_id', 'id');
    }

    public function quizzes ()
    {
        return $this->hasMany(Quizzes::class, 'quiz_question_id', 'id');
    }

    /**
     * each quiz has one region
     * @return belongsTo
     */
    public function region()
    {
        return $this->belongsTo(Regions::class, 'region_id', 'id');
    }
}
