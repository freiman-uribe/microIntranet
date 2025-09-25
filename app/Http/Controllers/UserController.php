<?php

namespace App\Http\Controllers;

use App\Contracts\UserServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(
        private UserServiceInterface $userService
    ) {
        // Middleware aplicado a nivel de rutas
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        // Solo admins pueden ver la lista de usuarios
        if (!auth()->user()->isAdmin()) {
            abort(403, 'No autorizado. Solo administradores pueden ver la lista de usuarios.');
        }
        
        $users = $this->userService->getPaginatedUsers(10);
        
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // Solo admins pueden crear usuarios
        if (!auth()->user()->isAdmin()) {
            abort(403, 'No autorizado. Solo administradores pueden crear usuarios.');
        }
        
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request): RedirectResponse
    {
        // Solo admins pueden crear usuarios
        if (!auth()->user()->isAdmin()) {
            abort(403, 'No autorizado. Solo administradores pueden crear usuarios.');
        }
        
        try {
            $this->userService->createUser($request->validated());
            
            return redirect()->route('users.index')
                ->with('success', 'Usuario creado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Error al crear el usuario: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): View
    {
        // Solo admins pueden ver otros usuarios, los usuarios normales solo su propio perfil
        if (!auth()->user()->isAdmin() && auth()->id() !== $user->id) {
            abort(403, 'No autorizado');
        }

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): View
    {
        // Solo admins pueden editar otros usuarios, los usuarios normales solo su propio perfil
        if (!auth()->user()->isAdmin() && auth()->id() !== $user->id) {
            abort(403, 'No autorizado');
        }

        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user): RedirectResponse
    {
        // Solo admins pueden editar otros usuarios, los usuarios normales solo su propio perfil
        if (!auth()->user()->isAdmin() && auth()->id() !== $user->id) {
            abort(403, 'No autorizado');
        }

        try {
            $validatedData = $request->validated();
            
            // Restricción de seguridad: Un admin no puede cambiar su propio rol
            if (auth()->user()->isAdmin() && auth()->id() === $user->id) {
                // Mantener el rol actual del admin que se está editando a sí mismo
                $validatedData['admin'] = $user->admin;
            }
            
            $this->userService->updateUser($user->id, $validatedData);
            
            // Determinar la redirección apropiada
            if (auth()->user()->isAdmin() && auth()->id() !== $user->id) {
                // Admin editando otro usuario → ir a lista de usuarios
                return redirect()->route('users.index')
                    ->with('success', 'Usuario actualizado correctamente.');
            } else {
                // Usuario editando su propio perfil → ir a su perfil
                return redirect()->route('users.show', $user)
                    ->with('success', 'Perfil actualizado correctamente.');
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Error al actualizar el usuario: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        // Solo admins pueden eliminar usuarios
        if (!auth()->user()->isAdmin()) {
            abort(403, 'No autorizado. Solo administradores pueden eliminar usuarios.');
        }
        
        // Evitar que se elimine a sí mismo
        if (auth()->id() === $user->id) {
            return redirect()->back()
                ->withErrors(['error' => 'No puedes eliminar tu propia cuenta.']);
        }
        
        try {
            $this->userService->deleteUser($user->id);
            
            return redirect()->route('users.index')
                ->with('success', 'Usuario eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Error al eliminar el usuario: ' . $e->getMessage()]);
        }
    }
}
