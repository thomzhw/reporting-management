<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class OutletStaff extends Pivot
{
    protected $table = 'outlet_staff';

    protected $fillable = [
        'outlet_id',
        'staff_id',
        'role',
        'assigned_by',
    ];

    public $incrementing = true;  // Since you have an id column
    protected $primaryKey = 'id';

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id');
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}