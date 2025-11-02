<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SectionNote extends Model
{
    //
    protected $fillable = ['section_id', 'user_id', 'note'];
    public function section()
    {
        return $this->belongsTo(Section::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
