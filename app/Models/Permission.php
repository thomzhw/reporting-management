<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'description',
        'created_at',
        'updated_at',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
