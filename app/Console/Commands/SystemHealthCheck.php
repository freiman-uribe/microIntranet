<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class SystemHealthCheck extends Command
{
    protected $signature = 'system:health-check';
    protected $description = 'Verifica el estado general del sistema Micro Intranet';

    public function handle()
    {
        $this->info('🏥 Verificación de Salud del Sistema - Micro Intranet');
        $this->newLine();

        // Verificar base de datos
        $this->checkDatabase();
        
        // Verificar usuarios
        $this->checkUsers();
        
        // Verificar notificaciones
        $this->checkNotifications();
        
        // Verificar archivos importantes
        $this->checkFiles();
        
        $this->newLine();
        $this->info('✅ Verificación de salud completada');
        
        return Command::SUCCESS;
    }

    private function checkDatabase()
    {
        $this->line('📊 Verificando Base de Datos...');
        
        try {
            $userCount = User::count();
            $this->line("   ✓ Conexión a BD: OK");
            $this->line("   ✓ Usuarios registrados: {$userCount}");
        } catch (\Exception $e) {
            $this->error("   ✗ Error de BD: " . $e->getMessage());
        }
    }

    private function checkUsers()
    {
        $this->line('👥 Verificando Usuarios...');
        
        $adminCount = User::where('admin', true)->count();
        $regularCount = User::where('admin', false)->count();
        $minorCount = User::where('edad', '<', 18)->count();
        
        $this->line("   ✓ Administradores: {$adminCount}");
        $this->line("   ✓ Usuarios regulares: {$regularCount}");
        $this->line("   ✓ Usuarios menores: {$minorCount}");
        
        if ($adminCount === 0) {
            $this->warn('   ⚠ No hay administradores registrados');
        }
    }

    private function checkNotifications()
    {
        $this->line('🔔 Verificando Notificaciones...');
        
        try {
            $totalNotifications = \DB::table('notifications')->count();
            $unreadCount = \DB::table('notifications')->whereNull('read_at')->count();
            
            $this->line("   ✓ Total notificaciones: {$totalNotifications}");
            $this->line("   ✓ Sin leer: {$unreadCount}");
        } catch (\Exception $e) {
            $this->error("   ✗ Error en notificaciones: " . $e->getMessage());
        }
    }

    private function checkFiles()
    {
        $this->line('📁 Verificando Archivos Críticos...');
        
        $files = [
            '.env' => base_path('.env'),
            'database.sqlite' => database_path('database.sqlite'),
            'storage/logs' => storage_path('logs'),
            'bootstrap/cache' => base_path('bootstrap/cache'),
        ];
        
        foreach ($files as $name => $path) {
            if (file_exists($path)) {
                $this->line("   ✓ {$name}: Existe");
            } else {
                $this->error("   ✗ {$name}: No encontrado");
            }
        }
    }
}