<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Notifications\DatabaseNotification;

class ShowNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:show {--admin-only : Show only admin notifications}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show all notifications in the system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔔 Sistema de Notificaciones - Micro Intranet');
        $this->newLine();

        $totalNotifications = DatabaseNotification::count();
        $this->info("📊 Total de notificaciones en el sistema: {$totalNotifications}");
        
        if ($this->option('admin-only')) {
            $admins = User::where('admin', true)->get();
            
            $this->info("👑 Notificaciones para Administradores:");
            $this->newLine();
            
            foreach ($admins as $admin) {
                $unreadCount = $admin->unreadNotifications()->count();
                $totalCount = $admin->notifications()->count();
                
                $this->comment("🧑‍💼 {$admin->nombre} {$admin->apellido} ({$admin->email})");
                $this->line("   📧 Total: {$totalCount} | 🆕 Sin leer: {$unreadCount}");
                
                if ($totalCount > 0) {
                    $notifications = $admin->notifications()->latest()->limit(3)->get();
                    foreach ($notifications as $notification) {
                        $status = is_null($notification->read_at) ? '🆕 Nueva' : '✅ Leída';
                        $type = $notification->data['type'] ?? 'general';
                        $title = $notification->data['title'] ?? 'Notificación';
                        $date = $notification->created_at->format('d/m/Y H:i');
                        
                        $this->line("      • {$status} | {$type} | {$title} | {$date}");
                        
                        if (isset($notification->data['user_data'])) {
                            $userData = $notification->data['user_data'];
                            $this->line("        👤 Usuario: {$userData['nombre']} {$userData['apellido']} ({$userData['edad']} años)");
                        }
                    }
                }
                $this->newLine();
            }
        } else {
            // Mostrar todas las notificaciones
            $notifications = DatabaseNotification::with('notifiable')->latest()->limit(10)->get();
            
            if ($notifications->count() === 0) {
                $this->warn('❌ No hay notificaciones en el sistema');
                return;
            }
            
            $this->info("📋 Últimas 10 notificaciones:");
            $this->newLine();
            
            $headers = ['ID', 'Usuario', 'Tipo', 'Título', 'Estado', 'Fecha'];
            $rows = [];
            
            foreach ($notifications as $notification) {
                $user = $notification->notifiable;
                $status = is_null($notification->read_at) ? '🆕 Nueva' : '✅ Leída';
                $type = $notification->data['type'] ?? 'general';
                $title = $notification->data['title'] ?? 'Notificación';
                
                $rows[] = [
                    substr($notification->id, 0, 8) . '...',
                    $user ? $user->nombre . ' ' . $user->apellido : 'Usuario eliminado',
                    $type,
                    $title,
                    $status,
                    $notification->created_at->format('d/m/Y H:i')
                ];
            }
            
            $this->table($headers, $rows);
        }

        $this->newLine();
        $this->info('💡 Comandos útiles:');
        $this->comment('  • php artisan notifications:show --admin-only');
        $this->comment('  • Visita: http://127.0.0.1:8000/notifications');

        return Command::SUCCESS;
    }
}
