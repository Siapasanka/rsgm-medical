<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AccountUserController extends Controller
{
    public function index(Request $request): View
    {
        $manageableRoles = $this->manageableRoles();
        $q = trim((string) $request->string('q'));
        $role = $request->string('role')->toString();

        $users = User::query()
            ->whereHas('role', function ($query) use ($role, $manageableRoles) {
                $query->whereIn('name', $manageableRoles);

                if (in_array($role, $manageableRoles, true)) {
                    $query->where('name', $role);
                }
            })
            ->with('role')
            ->when($q !== '', fn ($query) => $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('username', 'like', "%{$q}%");
            }))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('account-users.index', compact('users', 'q', 'role', 'manageableRoles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $manageableRoles = $this->manageableRoles();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'lowercase', 'max:255', 'unique:users,username'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:'.implode(',', $manageableRoles)],
        ]);

        $role = Role::where('name', $validated['role'])->firstOrFail();

        User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'role_id' => $role->id,
        ]);

        return back()->with('status', 'Akun berhasil dibuat.');
    }

    public function edit(User $user): View
    {
        $this->ensureManageableRole($user);

        return view('account-users.edit', [
            'user' => $user,
            'manageableRoles' => $this->manageableRoles(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->ensureManageableRole($user);
        $manageableRoles = $this->manageableRoles();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'lowercase', 'max:255', 'unique:users,username,'.$user->id],
            'role' => ['required', 'in:'.implode(',', $manageableRoles)],
        ]);

        $role = Role::where('name', $validated['role'])->firstOrFail();

        $user->update([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'role_id' => $role->id,
        ]);

        return redirect()->route('account-users.index')->with('status', 'Akun berhasil diperbarui.');
    }

    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        $this->ensureManageableRole($user);

        $validated = $request->validate([
            'new_password' => ['required', 'string', 'min:8'],
        ]);

        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        return back()->with('status', 'Password akun berhasil di-reset.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->ensureManageableRole($user);

        $user->delete();

        return back()->with('status', 'Akun berhasil dihapus.');
    }

    private function manageableRoles(): array
    {
        return auth()->user()?->role?->name === 'superadmin'
            ? ['admin', 'dokter']
            : ['dokter'];
    }

    private function ensureManageableRole(User $user): void
    {
        if (! in_array($user->role?->name, $this->manageableRoles(), true)) {
            abort(403, 'Role akun ini tidak bisa dikelola.');
        }
    }
}