<?php

namespace App\Models\ComponentsModels;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $id
 * @property string $option
 * @property string $text
 */
class ComponentsQuizAnswers extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'components_quiz_answers';

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
        'option', 'text'
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
        'id' => 'int', 'option' => 'string', 'text' => 'string'
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
