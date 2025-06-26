<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QaTemplateAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_id',
        'staff_id',
        'outlet_id', // New field
        'assigned_by',
        'due_date',
        'status',
        'assignment_reference',
        'notes'
    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];
    
    // Existing relationships
    public function template()
    {
        return $this->belongsTo(QaTemplate::class);
    }
    
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
    
    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
    
    public function report()
    {
        return $this->hasOne(QaReport::class, 'assignment_id');
    }
    
    // New relationship
    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }
}