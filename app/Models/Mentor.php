<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mentor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'welcome_statement',
        'years_of_experience',
        'skills',
    ];

    protected $casts = [
        'skills' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tracks()
    {
        return $this->belongsToMany(Track::class, 'mentor_track', 'mentor_id', 'track_id');
    }

    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function blogs()
    {
        return $this->hasMany(Blog::class);
    }

    public function resources()
    {
        return $this->hasMany(Resource::class);
    }
}
