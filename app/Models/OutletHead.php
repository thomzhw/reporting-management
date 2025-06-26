<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class OutletHead extends Pivot
{

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the outlet for this assignment.
     */
    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id');
    }

    /**
     * Get the head for this assignment.
     */
    public function head()
    {
        return $this->belongsTo(User::class, 'head_id');
    }
}