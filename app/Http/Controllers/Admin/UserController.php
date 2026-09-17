<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use App\Notifications\WelcomeSetPassword;
use App\Support\AdminModules;
use App\Support\PermissionPresets;
use App\Support\TeachingModules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->get();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $subjects = Subject::select('name')->distinct()->orderBy('name')->pluck('name');
        $moduleGroups = AdminModules::byCategory();
        $teachingModules = TeachingModules::all();
        $permissionPresets = PermissionPresets::all();

        return view('admin.users.create', compact('subjects', 'moduleGroups', 'teachingModules', 'permissionPresets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,supervisor,teacher',
            'phone' => 'required_if:role,teacher|nullable|string|max:255',
            'specialization' => 'required_if:role,teacher|nullable|string|max:255',
            'hire_date' => 'required_if:role,teacher|nullable|date',
            'salary_type' => 'required_if:role,teacher|nullable|in:fixed,hourly',
            'fixed_salary' => 'nullable|numeric|min:0',
            'hourly_rate' => 'nullable|numeric|min:0',
            'is_partner' => 'nullable|boolean',
            'permissions' => 'nullable|array',
            'permissions.*' => 'in:' . implode(',', array_merge(AdminModules::keys(), TeachingModules::keys())),
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => $validated['role'],
            'permissions' => $validated['role'] === 'admin' ? null : ($validated['permissions'] ?? []),
        ]);

        if ($validated['role'] === 'teacher') {
            $this->createTeacherRecord($user, $validated, $request);
        }

        return redirect()->route('admin.users.create')->with('success', __('messages.flash_user_created'));
    }

    public function edit(User $user)
    {
        $teacher = Teacher::where('user_id', $user->id)->first();
        $subjects = Subject::select('name')->distinct()->orderBy('name')->pluck('name');
        $moduleGroups = AdminModules::byCategory();
        $teachingModules = TeachingModules::all();
        $permissionPresets = PermissionPresets::all();

        return view('admin.users.edit', compact('user', 'teacher', 'subjects', 'moduleGroups', 'teachingModules', 'permissionPresets'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,supervisor,teacher',
            'phone' => 'required_if:role,teacher|nullable|string|max:255',
            'specialization' => 'required_if:role,teacher|nullable|string|max:255',
            'hire_date' => 'required_if:role,teacher|nullable|date',
            'salary_type' => 'required_if:role,teacher|nullable|in:fixed,hourly',
            'fixed_salary' => 'nullable|numeric|min:0',
            'hourly_rate' => 'nullable|numeric|min:0',
            'is_partner' => 'nullable|boolean',
            'permissions' => 'nullable|array',
            'permissions.*' => 'in:' . implode(',', array_merge(AdminModules::keys(), TeachingModules::keys())),
        ]);

        $wasTeacher = $user->role === 'teacher';

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        $user->permissions = $validated['role'] === 'admin' ? null : ($validated['permissions'] ?? []);

        if (! empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }

        $user->save();

        if ($validated['role'] === 'teacher') {
            if (! $wasTeacher) {
                $this->createTeacherRecord($user, $validated, $request);
            } else {
                Teacher::where('user_id', $user->id)->update([
                    'first_name' => explode(' ', $user->name, 2)[0],
                    'last_name' => explode(' ', $user->name, 2)[1] ?? '',
                    'email' => $user->email,
                    'phone' => $validated['phone'],
                    'specialization' => $validated['specialization'],
                    'hire_date' => $validated['hire_date'],
                    'salary_type' => $validated['salary_type'],
                    'fixed_salary' => $validated['fixed_salary'] ?? null,
                    'hourly_rate' => $validated['hourly_rate'] ?? null,
                    'is_partner' => $request->boolean('is_partner'),
                ]);
            }
        }

        return redirect()->route('admin.users.index')->with('success', __('messages.flash_user_updated'));
    }

    public function sendResetLink(User $user)
    {
        $token = Password::createToken($user);
        $user->notify(new WelcomeSetPassword($token));

        return back()->with('success', __('messages.flash_reset_link_sent'));
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', __('messages.flash_user_cannot_delete_own_account'));
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', __('messages.flash_user_deleted'));
    }

    private function createTeacherRecord(User $user, array $validated, Request $request): void
    {
        if (Teacher::where('user_id', $user->id)->exists()) {
            return;
        }

        $nameParts = explode(' ', $user->name, 2);
        $employeeNumber = 'T' . str_pad((Teacher::max('id') ?? 0) + 1, 4, '0', STR_PAD_LEFT);

        Teacher::create([
            'user_id' => $user->id,
            'employee_number' => $employeeNumber,
            'first_name' => $nameParts[0],
            'last_name' => $nameParts[1] ?? '',
            'phone' => $validated['phone'],
            'email' => $user->email,
            'specialization' => $validated['specialization'],
            'hire_date' => $validated['hire_date'],
            'status' => 'active',
            'salary_type' => $validated['salary_type'],
            'fixed_salary' => $validated['fixed_salary'] ?? null,
            'hourly_rate' => $validated['hourly_rate'] ?? null,
            'is_partner' => $request->boolean('is_partner'),
        ]);
    }
}