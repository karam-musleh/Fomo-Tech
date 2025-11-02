<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Blog extends Model
{
    use HasFactory;
    use HasSlug ;
    //status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_PUBLISHED = 'published';
    const STATUS_REJECTION = 'rejection';


    //
    protected $fillable = [
        'title',
        'description',
        'slug',
        'image',
        'status',
        'rejection_reason',
        'mentor_id',
        'section_id'

    ];

    public function mentor()
    {
        return $this->belongsTo(Mentor::class);
    }
    public function section()
    {
        return $this->belongsTo(Section::class);
    }
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
