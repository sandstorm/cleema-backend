<?php

namespace App\Models\NewsEntryModels;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $news_entry_id
 * @property int $news_tag_id
 * @property int $news_tag_order
 */
class NewsEntriesTagsLinks extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'news_entries_tags_links';

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
        'news_entry_id', 'news_tag_id', 'news_tag_order'
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
        'id' => 'int', 'news_entry_id' => 'int', 'news_tag_id' => 'int', 'news_tag_order' => 'int'
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