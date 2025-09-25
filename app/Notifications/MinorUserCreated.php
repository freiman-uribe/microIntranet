<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MinorUserCreated extends Notification
{
    use Queueable;

    public $minorUser;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $minorUser)
    {
        $this->minorUser = $minorUser;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🚨 Usuario Menor de Edad Registrado - Micro Intranet')
            ->greeting('Hola ' . $notifiable->nombre . ',')
            ->line('Se ha registrado un **usuario menor de edad** en el sistema.')
            ->line('**Detalles del usuario:**')
            ->line('• **Nombre:** ' . $this->minorUser->nombre . ' ' . $this->minorUser->apellido)
            ->line('• **Email:** ' . $this->minorUser->email)
            ->line('• **Edad:** ' . $this->minorUser->edad . ' años')
            ->line('• **Fecha de registro:** ' . $this->minorUser->created_at->format('d/m/Y H:i:s'))
            ->action('Ver Usuario en el Sistema', url('/users/' . $this->minorUser->id))
            ->line('Como administrador, debes revisar esta cuenta según las políticas de la empresa.')
            ->salutation('Saludos, Sistema Micro Intranet');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'minor_user_created',
            'title' => '🚨 Usuario Menor de Edad Registrado',
            'message' => 'Se registró un usuario menor de edad: ' . $this->minorUser->nombre . ' ' . $this->minorUser->apellido . ' (' . $this->minorUser->edad . ' años)',
            'user_data' => [
                'id' => $this->minorUser->id,
                'nombre' => $this->minorUser->nombre,
                'apellido' => $this->minorUser->apellido,
                'email' => $this->minorUser->email,
                'edad' => $this->minorUser->edad,
                'created_at' => $this->minorUser->created_at->toISOString(),
            ],
            'action_url' => url('/users/' . $this->minorUser->id),
            'priority' => 'high'
        ];
    }

    /**
     * Get the notification's database type.
     */
    public function databaseType(object $notifiable): string
    {
        return 'minor_user_alert';
    }
}