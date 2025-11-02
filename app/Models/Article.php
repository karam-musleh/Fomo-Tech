<?php

namespace App\Models;

use App\Models\Track;
use App\Models\Mentor;
use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Article extends Model
{

    //
    use HasFactory;
    use HasSlug;

    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';
    const STATUS_UNDER_REVIEW = 'under_review';

    protected $fillable = [
        'title',
        'content',
        'slug',
        'mentor_id',
        'status'
    ];

    public function mentor()
    {
        return $this->belongsTo(Mentor::class);
    }
    public function tracks()
    {
        return $this->belongsToMany(Track::class, 'article_track', 'article_id', 'track_id');
    }
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
