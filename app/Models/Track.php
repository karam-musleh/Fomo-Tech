<?php

namespace App\Models;

use App\Models\Mentor;
use App\Models\Article;
use App\Models\Section;
use App\Models\Resource;
use App\Models\User;
use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Track extends Model
{
    use HasFactory;
    // use HasSlug ;
    const STATUS_DRAFT = 'draft';
    const STATUS_PUBLISHED = 'published';


    protected $fillable = [
        'name',
        'slug',
        'image',
        'description',
        'introduction',
        'content',
        'status',
        'rejection_reason',
        'section_id',
    ];

    public function mentors()
    {
        return $this->belongsToMany(Mentor::class, 'mentor_track');
    }

    public function resources()
    {
        return $this->belongsToMany(Resource::class, 'track_resource');
    }

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'article_track', 'track_id', 'article_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function favoredByUsers()
    {
        return $this->belongsToMany(User::class, 'favorite_tracks', 'track_id', 'user_id')
            ->withTimestamps();
    }
    // public function scopePublished($query, $status = 'published')
    // {
    //     return $query->where('status', $status);
    // }
}
