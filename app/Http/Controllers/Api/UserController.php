<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserIndexRequest;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(UserIndexRequest $request)
    {
        $validated = $request->validated();

        $users = User::query()
            ->when($validated['search'] ?? null, fn($q, $search) =>
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
            )
            ->with('roles')
            ->orderByDesc('id')
            ->paginate($validated['per_page'] ?? 10);

        return ApiResponse::success($users);
    }

    public function all()
    {
        return ApiResponse::success(
            User::orderBy('name')->get(),
        );
    }

    public function store(UserStoreRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);
        
        $user->assignRole($validated['role']);
        dd($validated['role']);
        return ApiResponse::success($user->load('roles'), 'Usuario creado correctamente', 201);
    }

    public function update(UserUpdateRequest $request, User $user)
    {
        $validated = $request->validated();

        $user->name  = $validated['name'];
        $user->email = $validated['email'];

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        if (isset($validated['role'])) {
            $user->syncRoles([$validated['role']]);
        }

        $user->save();

        return ApiResponse::success($user->load('roles'), 'Usuario actualizado correctamente');
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return ApiResponse::error('No puedes eliminar tu propio usuario', 403);
        }

        $user->delete();

        return ApiResponse::success(null, 'Usuario eliminado correctamente');
    }
}