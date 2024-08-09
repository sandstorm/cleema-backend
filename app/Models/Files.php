<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Files extends Model
{
    public static function boot(): void
    {
        parent::boot();

        self::creating(function ($file) {
            $file->created_by_id = auth()->id();
            $file->uuid = Str::uuid();
        });

        self::saving(function ($file) {
            $file->updated_by_id = auth()->id();
            if ($file->uuid == null) {
                $file->uuid = Str::uuid();
            }
        });
    }

    use HasFactory;

    protected $table = 'files';

    protected $fillable = [
        'id','name','alternative_text','caption','width','height','formats','hash','ext','mime','size','url','preview_url','provider','provider_metadata','folder_path','created_at','updated_at','created_by_id','updated_by_id', 'uuid'
    ];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    public function trophies() {
        return $this->hasMany(Trophies::class, 'id', 'image_id');
    }
    public function user_avatars() {
        return $this->hasMany(Trophies::class, 'id', 'image_id');
    }

    public function projectsImage()
    {
        return $this->hasMany(Projects::class, 'id', 'image_id');
    }

    public function projectsTeaserImage()
    {
        return $this->hasMany(Projects::class, 'id', 'teaser_image_id');
    }

    public function newsEntriesImage()
    {
        return $this->hasMany(NewsEntries::class, 'id', 'image_id');
    }

    public function challengeImages()
    {
        return $this->hasMany(ChallengeImages::class, 'id', 'image_id');
    }

    public function partnersLogo()
    {
        return $this->hasMany(Partners::class, 'id', 'logo_id');
    }

    public function offersImage()
    {
        return $this->hasMany(Offers::class, 'id', 'image_id');
    }
}
