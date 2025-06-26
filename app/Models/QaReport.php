<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QaReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_id', 
        'staff_id', 
        'assignment_id', 
        'completed_at', 
        'reviewed_at',     
        'reviewed_by',     
        'status',          
        'feedback',         
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];
    
    public function template()
    {
        return $this->belongsTo(QaTemplate::class);
    }
    
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
    
    public function responses()
    {
        return $this->hasMany(QaReportResponse::class, 'report_id');
    }

    public function assignment()
    {
        return $this->belongsTo(QaTemplateAssignment::class);
    }
    
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
