<?php

namespace App\Models;

use App\Models\Track;
use App\Models\Section;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Resource extends Model
{
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';
    const STATUS_UNDER_REVIEW = 'under_review';
    const TYPE_COURSE = 'Course';
    const TYPE_BOOK = 'Book';
    const TYPE_TOOL = 'Tool';
    //
    use HasFactory;

    protected $fillable = [
        'name',
        'link',
        'type',
        'slug',
        'status',
        'rejection_reason',
        'mentor_id',

    ];
    public function sections()
    {
        return $this->belongsToMany(Section::class, 'resource_section');
    }
    public function tracks()
    {
        return $this->belongsToMany(Track::class, 'track_resource');
    }
    function mentor()
    {
        return $this->belongsTo(Mentor::class);
    }
}
