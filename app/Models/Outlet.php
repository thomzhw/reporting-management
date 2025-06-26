<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Outlet extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'name',
        'address',
        'city',
        'phone',
        'email',
        'description',
        'type',
        'status'
    ];
    
    public function heads()
    {
        return $this->belongsToMany(User::class, 'outlet_head', 'outlet_id', 'head_id')
                    ->withTimestamps();
    }

    public function staffs()
    {
        return $this->belongsToMany(User::class, 'outlet_staff', 'outlet_id', 'staff_id')
                    ->using(OutletStaff::class)  // Use the custom pivot model
                    ->withPivot(['role', 'assigned_by'])
                    ->withTimestamps();
    }
    
    public function qaTemplates()
    {
        return $this->hasMany(QaTemplate::class);
    }
}