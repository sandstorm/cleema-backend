<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $day_index
 * @property string $answer
 * @property string $joined_challenge_id
 */
class JoinedChallengesAnswers extends Model
{
    use HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'joined_challenges_answers';

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
    protected $fillable = ['answer', 'day_index', 'joined_challenge_id'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'int', 'answer' => 'string', 'day_index' => 'int', 'joined_challenge_id' => 'int'
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * each joined Challenge has multiple answers
     * @return BelongsTo
     */
    public function joinedChallenge()
    {
        return $this->belongsTo(JoinedChallenges::class, 'joined_challenge_id', 'id');
    }

}
