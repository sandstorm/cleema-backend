<?php

namespace App\Models\ChallengeModels;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $challenge_image_id
 * @property int $challenge_template_id
 * @property int $challenge_template_order
 */
class ChallengeTemplatesImageLinks extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'challenge_templates_image_links';

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
        'challenge_image_id', 'challenge_template_id', 'challenge_template_order'
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
        'id' => 'int', 'challenge_image_id' => 'int', 'challenge_template_id' => 'int', 'challenge_template_order' => 'int'
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
