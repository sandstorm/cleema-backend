<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @property int      $id
 * @property int      $created_by_id
 * @property int      $updated_by_id
 * @property string   $uuid
 * @property DateTime $created_at
 * @property DateTime $published_at
 * @property DateTime $updated_at
 * @property Date     $date
 * @property string   $quiz_question_id
 */
class Quizzes extends Model
{
    use HasFactory;

    public static function boot(): void
    {
        parent::boot();

        // We use the "date" in the API to determine which Quiz is the one to be displayed, because published_at
        // also saves the time of day. In case we need published_at again in the future, we make sure published_at and
        // date always align, we set them here
        self::creating(function ($quizzes) {
            $quizzes->uuid = Str::uuid();
            $quizzes->created_by_id = auth()->id();
            $quizzes->published_at = $quizzes->date;
        });

        self::saving(function ($quizzes) {
            $quizzes->updated_by_id = auth()->id();
            $quizzes->published_at = $quizzes->date;
        });
    }

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'quizzes';

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
        'created_at',
        'created_by_id',
        'date',
        'published_at',
        'updated_at',
        'updated_by_id',
        'uuid',
        'quiz_question_id'
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
        'created_at' => 'datetime',
        'created_by_id' => 'int',
        'date' => 'date',
        'published_at' => 'datetime',
        'updated_at' => 'datetime',
        'updated_by_id' => 'int',
        'uuid' => 'string',
        'quiz_question_id' => 'int'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'date', 'published_at', 'updated_at'
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var boolean
     */
    public $timestamps = true;

    public function responses()
    {
        return $this->belongsToMany(UpUsers::class, 'quiz_responses_v2','quiz_id', 'user_id')->withPivot(['answer', 'date']);
    }

    public function quizQuestion ()
    {
        return $this->belongsTo(QuizQuestions::class, 'quiz_question_id', 'id');
    }
}
