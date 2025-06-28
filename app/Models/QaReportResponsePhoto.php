<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QaReportResponsePhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'response_id',
        'photo_path',
        'order',
    ];

    public function response()
    {
        return $this->belongsTo(QaReportResponse::class, 'response_id');
    }
    
    public function getPhotoUrlAttribute()
    {
        return asset('storage/' . $this->photo_path);
    }
}