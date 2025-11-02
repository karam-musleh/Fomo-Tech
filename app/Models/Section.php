<?php

namespace App\Models;

use App\Models\Blog;
use App\Models\Track;
use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Section extends Model
{
    use HasFactory ;
    use HasSlug;

     const STATUS_DRAFT = 'draft';
    const STATUS_PUBLISHED = 'published';

    protected $fillable = [
        'title',
        'image',
        'description',
        'details',
        'status' ,
        'slug'
    ];
    //
        public function resources()
{
    return $this->belongsToMany(Resource::class, 'resource_section');
}
    public function blogs()
    {
        return $this->hasMany(Blog::class);
    }
    public function tracks()
{
    return $this->hasMany(Track::class);
}
    public function sectionNotes()
{
    return $this->hasMany(SectionNote::class);
}

}
