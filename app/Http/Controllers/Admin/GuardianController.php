<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guardian;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GuardianController extends Controller
{
    public function index()
    {
        $guardians = Guardian::withCount('students')->orderBy('name')->get();
        return view('admin.guardians.index', compact('guardians'));
    }

    public function create()
    {
        return view('admin.guardians.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'profession' => 'nullable|string|max:255',
            'phone1' => ['required', 'string', 'max:50', Rule::unique('users', 'phone')],
            'whatsapp' => 'required|string|max:50',
            'phone2' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $guardian = Guardian::create($validated);

        $this->createLoginAccount($guardian, $validated['password']);

        if ($request->wantsJson()) {
            return response()->json([
                'id' => $guardian->id,
                'name' => $guardian->name,
                'phone1' => $guardian->phone1,
            ]);
        }

        return redirect()->route('admin.guardians.index')
            ->with('success', __('messages.flash_guardian_created'));
    }

    public function edit(Guardian $guardian)
    {
        return view('admin.guardians.edit', compact('guardian'));
    }

    public function update(Request $request, Guardian $guardian)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'profession' => 'nullable|string|max:255',
            'phone1' => ['required', 'string', 'max:50', Rule::unique('users', 'phone')->ignore($guardian->user_id)],
            'whatsapp' => 'required|string|max:50',
            'phone2' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'password' => $guardian->user ? 'nullable|string|min:6|confirmed' : 'required|string|min:6|confirmed',
        ]);

        $guardian->update($validated);

        if ($guardian->user) {
            $guardian->user->update([
                'name' => $guardian->name,
                'phone' => $guardian->phone1,
            ]);

            if (! empty($validated['password'])) {
                $guardian->user->update(['password' => bcrypt($validated['password'])]);
            }
        } else {
            $this->createLoginAccount($guardian, $validated['password']);
        }

        return redirect()->route('admin.guardians.index')
            ->with('success', __('messages.flash_guardian_updated'));
    }

    public function destroy(Guardian $guardian)
    {
        $guardian->user?->delete();
        $guardian->delete();
        return redirect()->route('admin.guardians.index')
            ->with('success', __('messages.flash_guardian_deleted'));
    }

    private function createLoginAccount(Guardian $guardian, string $password): void
    {
        $user = User::create([
            'name' => $guardian->name,
            'email' => 'guardian'.$guardian->id.'@parents.local',
            'phone' => $guardian->phone1,
            'password' => bcrypt($password),
            'role' => 'guardian',
            'locale' => app()->getLocale(),
        ]);

        $guardian->update(['user_id' => $user->id]);
    }
}
