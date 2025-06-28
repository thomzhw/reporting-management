<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QaRulePhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'rule_id',
        'photo_path',
        'caption',
        'order'
    ];

    // Relationship to rule
    public function rule()
    {
        return $this->belongsTo(QaRule::class);
    }

    // Accessor for full URL
    public function getPhotoUrlAttribute()
    {
        return asset('storage/'.$this->photo_path);
    }
}