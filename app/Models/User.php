<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, SoftDeletes, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'head_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function hasAccess($permissionSlug)
    {
        return $this->role->permissions->contains('slug', $permissionSlug);
    }
    // In your User model, add these relationships:

    // Users that this user manages (as head)
    public function staffMembers()
    {
        return $this->hasMany(User::class, 'head_id');
    }

    // The head this user reports to
    public function head()
    {
        return $this->belongsTo(User::class, 'head_id');
    }

    // For QA templates created by this head
    public function qaTemplates()
    {
        return $this->hasMany(QaTemplate::class, 'head_id');
    }

    // For QA reports submitted by this staff
    public function qaReports()
    {
        return $this->hasMany(QaReport::class, 'staff_id');
    }

    // For template assignments (as a staff)
    public function templateAssignments()
    {
        return $this->hasMany(QaTemplateAssignment::class, 'staff_id');
    }

    // For templates assigned to others (as a head)
    public function assignedTemplates()
    {
        return $this->hasMany(QaTemplateAssignment::class, 'assigned_by');
    }

    public function managedOutlets()
    {
        return $this->belongsToMany(Outlet::class, 'outlet_head', 'head_id', 'outlet_id')
                    ->using(OutletHead::class)
                    ->withTimestamps();
    }

    public function assignedOutlets()
    {
        return $this->belongsToMany(Outlet::class, 'outlet_staff', 'staff_id', 'outlet_id')
                    ->using(OutletStaff::class)
                    ->withPivot('role', 'assigned_by')
                    ->withTimestamps();
    }

    public function staffAssignments()
    {
        return $this->hasMany(OutletStaff::class, 'assigned_by');
    }

}
