<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QaRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_id',
        'title',
        'description',
        'requires_photo',
        'photo_example_path',
        'order'
    ];

    public function template()
    {
        return $this->belongsTo(QaTemplate::class);
    }

    public function getPhotoExampleUrlAttribute()
    {
        return $this->photo_example_path ? asset('storage/'.$this->photo_example_path) : null;
    }
}
