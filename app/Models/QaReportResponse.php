<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QaReportResponse extends Model
{
    use HasFactory;

    protected $fillable = ['report_id', 'rule_id', 'response', 'photo_path'];
    
    public function report()
    {
        return $this->belongsTo(QaReport::class);
    }
    
    public function rule()
    {
        return $this->belongsTo(QaRule::class);
    }

    public function photos()
    {
        return $this->hasMany(QaReportResponsePhoto::class, 'response_id');
    }
}
