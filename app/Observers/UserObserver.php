<?php

namespace App\Observers;

use App\Models\User;
use App\Notifications\MinorUserCreated;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Notificar al admin si el usuario es menor de 18 años
        if ($user->edad < 18) {
            // Obtener todos los administradores
            $admins = User::where('admin', true)->get();
            
            foreach ($admins as $admin) {
                // Enviar notificación a la base de datos y email
                $admin->notify(new MinorUserCreated($user));
                
                // También mantener log para auditoría
                Log::warning("ALERTA: Usuario menor de edad creado", [
                    'usuario_menor_id' => $user->id,
                    'usuario_menor' => $user->nombre . ' ' . $user->apellido,
                    'edad' => $user->edad,
                    'email' => $user->email,
                    'admin_notificado' => $admin->email,
                    'fecha' => now()
                ]);
            }
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // Verificar si la edad cambió y ahora es menor de 18
        if ($user->wasChanged('edad') && $user->edad < 18) {
            $admins = User::where('admin', true)->get();
            
            foreach ($admins as $admin) {
                $admin->notify(new MinorUserCreated($user));
                
                Log::warning("ALERTA: Usuario modificado ahora es menor de edad", [
                    'usuario_menor_id' => $user->id,
                    'usuario_menor' => $user->nombre . ' ' . $user->apellido,
                    'edad_anterior' => $user->getOriginal('edad'),
                    'edad_nueva' => $user->edad,
                    'email' => $user->email,
                    'admin_notificado' => $admin->email,
                    'fecha' => now()
                ]);
            }
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
