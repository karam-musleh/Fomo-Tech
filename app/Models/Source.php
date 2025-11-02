<?php

namespace App\Models;

use App\Models\Section;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Source extends Model
{
    //
        use HasFactory;

    protected $fillable = [
        'name',
        'link',
        'type',
    ];
    public function sections()
{
    return $this->belongsToMany(Section::class, 'source_section');
}

}
