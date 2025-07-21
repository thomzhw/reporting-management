<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use App\Models\QaReport;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SuperuserController extends Controller
{
    public function dashboard()
    {
        // Statistik Umum
        $stats = [
            'outlets_count' => Outlet::count(),
            'users_count' => User::count(),
            'reports_count' => QaReport::count(),
            'roles_count' => Role::count(),
        ];
        
        // Data Pengguna berdasarkan Role
        $superuserCount = User::whereHas('role', function($query) {
            $query->where('name', 'superuser');
        })->count();
        
        $headCount = User::whereHas('role', function($query) {
            $query->where('name', 'timhub');
        })->count();
        
        $staffCount = User::whereHas('role', function($query) {
            $query->where('name', 'staff');
        })->count();
        
        $otherCount = User::whereDoesntHave('role', function($query) {
            $query->whereIn('name', ['superuser', 'timhub', 'staff']);
        })->count();
        
        $roleData = [
            'superuserCount' => $superuserCount, 
            'headCount' => $headCount, 
            'staffCount' => $staffCount, 
            'otherCount' => $otherCount
        ];
        
        // Aktivitas Terbaru
        $recentReports = QaReport::with(['staff', 'template'])
            ->latest('completed_at')
            ->take(5)
            ->get();
        
        $recentUsers = User::with('role')
            ->latest()
            ->take(3)
            ->get();
        
        $activity = [
            'recentReports' => $recentReports,
            'recentUsers' => $recentUsers,
        ];
        
        // Analisis Status Outlet
        $outlets = Outlet::withCount(['heads', 'staffs'])->get();
        $totalOutlets = $outlets->count();
        
        $outletsWithBothHeadStaff = $outlets->filter(function($outlet) {
            return $outlet->heads_count > 0 && $outlet->staffs_count > 0;
        })->count();
        
        $outletsWithHeadOnly = $outlets->filter(function($outlet) {
            return $outlet->heads_count > 0 && $outlet->staffs_count == 0;
        })->count();
        
        $outletsWithStaffOnly = $outlets->filter(function($outlet) {
            return $outlet->heads_count == 0 && $outlet->staffs_count > 0;
        })->count();
        
        $outletsWithNeither = $outlets->filter(function($outlet) {
            return $outlet->heads_count == 0 && $outlet->staffs_count == 0;
        })->count();
        
        $outletsData = [
            'totalOutlets' => $totalOutlets,
            'outletsWithBothHeadStaff' => $outletsWithBothHeadStaff,
            'outletsWithHeadOnly' => $outletsWithHeadOnly,
            'outletsWithStaffOnly' => $outletsWithStaffOnly,
            'outletsWithNeither' => $outletsWithNeither,
        ];
        
        // Analisis QA Reports
        $totalReports = QaReport::count();
        $pendingReviewReports = QaReport::where('status', 'pending_review')->count();
        $approvedReports = QaReport::where('status', 'approved')->count();
        $rejectedReports = QaReport::where('status', 'rejected')->count();
        
        $reportsData = [
            'totalReports' => $totalReports,
            'pendingReviewReports' => $pendingReviewReports,
            'approvedReports' => $approvedReports,
            'rejectedReports' => $rejectedReports,
        ];
        
        // Data tabel
        $latestUsers = User::with('role')
            ->latest()
            ->take(5)
            ->get();
        
        $latestOutlets = Outlet::withCount(['heads', 'staffs'])
            ->latest()
            ->take(5)
            ->get();
        
        $tableData = [
            'latestUsers' => $latestUsers,
            'latestOutlets' => $latestOutlets,
        ];
        
        // Kirim semua data ke view
        return view('superuser.dashboard', compact(
            'stats', 
            'roleData', 
            'activity', 
            'outletsData',
            'reportsData',
            'tableData'
        ));
    }

    public function manageUsers()
    {
        $users = User::withTrashed()->with('role')->get();
        $roles = Role::all();
        return view('superuser.users', compact('users', 'roles'));
    }

    public function updateUserRole(Request $request, User $user)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id'
        ]);
        
        $user->update(['role_id' => $request->role_id]);
        
        return back()->with('success', 'User role updated successfully!');
    }

    public function createUserForm()
    {
        // Pastikan superuser yang akses ke sini punya izin untuk membuat user
        $this->middleware('permission:user.create'); 
        $roles = Role::all(); // Perlu daftar peran untuk dipilih
        return view('superuser.users.create', compact('roles'));
    }

    public function storeUser(Request $request)
    {
        $this->middleware('permission:user.create'); 

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')],
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            // 'email_verified_at' => now(), // Opsional: Verifikasi langsung jika dibuat oleh admin
            // 'created_by' => Auth::id(), // Opsional: Rekam siapa yang membuat user
        ]);

        return redirect()->route('users.manage')->with('success', 'User created successfully!');
    }

    public function editUser(User $user)
    {
        // Pastikan superuser yang akses ke sini punya izin untuk mengedit user
        $this->middleware('permission:user.manage'); // Atau izin yang lebih spesifik 'user.edit'
        $roles = Role::all();
        return view('superuser.users.edit', compact('user', 'roles'));
    }

    public function updateUser(Request $request, User $user)
    {
        $this->middleware('permission:user.manage'); 

        $request->validate([
            'name' => 'required|string|max:255',
            // Pastikan email unik kecuali jika emailnya tidak berubah untuk user yang sedang diedit
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)], 
            'password' => 'nullable|string|min:8|confirmed', // Password opsional, hanya jika ingin diubah
            'role_id' => 'required|exists:roles,id',
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
            // 'updated_by' => Auth::id(), // Opsional: Rekam siapa yang mengupdate user
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        return redirect()->route('users.manage')->with('success', 'User updated successfully!');
    }

    // public function destroyUser(User $user)
    // {
    //     $this->middleware('permission:user.manage'); 
        
    //     // Pencegahan: Jangan biarkan superuser menghapus dirinya sendiri atau superuser lain
    //     if ($user->id === auth()->id() || $user->hasAccess('superuser.access')) {
    //         abort(403, 'You cannot delete yourself or another superuser.');
    //     }

    //     $user->delete(); // Ini adalah soft delete karena model User menggunakan SoftDeletes
    //     return back()->with('success', 'User soft-deleted successfully!');
    // }

    public function destroyUser(User $user)
    {
        // Prevent deleting superusers (by role name) or self
        if ($user->id === auth()->id() || $user->role->name === 'superuser') {
            abort(403, 'Cannot delete superusers or yourself.');
        }

        $user->delete();
        return back()->with('success', 'User deactivated!');
    }

    // public function restoreUser($userId) // Menggunakan $userId karena user mungkin sudah soft-deleted
    // {
    //     $this->middleware('permission:user.manage'); 

    //     $user = User::withTrashed()->findOrFail($userId); // Temukan user termasuk yang soft-deleted

    //     // Pencegahan: Hanya user yang soft-deleted yang bisa dipulihkan
    //     if ($user->deleted_at === null) {
    //         abort(400, 'User is not soft-deleted.');
    //     }

    //     $user->restore(); // Memulihkan user
    //     return back()->with('success', 'User restored successfully!');
    // }

    public function restoreUser($userId)
    {
        $user = User::withTrashed()->findOrFail($userId);

        if (!$user->trashed()) {
            return back()->withErrors(['user' => 'User is not deleted']);
        }

        // Prevent restoring superusers
        if ($user->role->name === 'superuser') {
            abort(403, 'Cannot restore superusers.');
        }

        $user->restore();
        return back()->with('success', 'User restored!');
    }    
}
