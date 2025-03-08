<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Gate;


class UserController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        // Vérifie les autorisations pour toutes les méthodes
        $this->authorizeResource(User::class, 'user');
    }

    public function index()
    {
        // Vérifie si l'utilisateur peut voir la liste
        $this->authorize('viewAny', User::class);

        $users = User::where('role', '!=', 'super_admin')
            ->where('id', '!=', Auth::id())
            ->get()
            ->map(function ($user) {
                return [    
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'created_at' => $user->created_at->format('d/m/Y'),
                    'can' => [
                        'edit' => Gate::allows('updateRole', $user),
                        'delete' => Gate::allows('delete', $user)
                    ]
                ];
            });
        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'can' => [
                'create' => Gate::allows('create', User::class),
                'viewAny' => Gate::allows('viewAny', User::class)
            ]
        ]);
    }

    public function updateRole(Request $request, User $user)
    {
        // Vérifie si l'utilisateur peut modifier les rôles
        $this->authorize('updateRole', $user);

        $request->validate([
            'role' => ['required', 'in:admin,user']
        ]);

        $user->update(['role' => $request->role]);

        return back()->with('success', 'Rôle mis à jour avec succès.');
    }

    public function destroy(User $user)
    {
        // Vérifie si l'utilisateur peut supprimer
        $this->authorize('delete', $user);

        $user->delete();

        return back()->with('success', 'Utilisateur supprimé avec succès.');
    }
}