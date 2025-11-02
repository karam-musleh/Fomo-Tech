<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Mentor;
use App\Models\Article;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
        'date_of_birth',
        'pronoun',
        'major',
        'profile_photo',
        'goals',
        'bio',
        'linkedin_url',
    ];
    protected $casts = [
        'date_of_birth' => 'date',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    public function mentor()
    {
        return $this->hasOne(Mentor::class);
    }
    public function favoriteTracks()
    {
        return $this->belongsToMany(Track::class, 'favorite_tracks')
            ->withTimestamps();
    }

    public function savedArticles()
    {
        return $this->belongsToMany(Article::class, 'saved_articles')->withTimestamps();
    }

    public function savedBlogs()
    {
        return $this->belongsToMany(Blog::class, 'saved_blogs')->withTimestamps();
    }
    public function sectionNotes()
    {
        return $this->hasMany(SectionNote::class);
    }

    // public function articles()
    // {
    //     return $this->hasMany(Article::class, 'mentor_id');
    // }
}
