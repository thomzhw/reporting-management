<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QaTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'head_id', 
        'outlet_id', // New field
        'name', 
        'description',
        'category' // New field
    ];
    
    // Existing relationships
    public function head()
    {
        return $this->belongsTo(User::class, 'head_id');
    }
    
    public function rules()
    {
        return $this->hasMany(QaRule::class, 'template_id');
    }
    
    public function reports()
    {
        return $this->hasMany(QaReport::class, 'template_id');
    }
    
    // New relationship
    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }
    
    // New relationship for assignments
    public function assignments()
    {
        return $this->hasMany(QaTemplateAssignment::class, 'template_id');
    }
}