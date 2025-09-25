#!/usr/bin/env php
<?php
/**
 * Script para extraer enlaces de recuperación de contraseña del log de Laravel
 */

$logFile = __DIR__ . '/storage/logs/laravel.log';

if (!file_exists($logFile)) {
    echo "❌ No se encontró el archivo de log: $logFile\n";
    exit(1);
}

echo "🔍 Buscando enlaces de recuperación de contraseña en el log...\n\n";

$content = file_get_contents($logFile);
$lines = explode("\n", $content);

$found = false;
$currentEmail = null;

foreach (array_reverse($lines) as $line) {
    // Buscar emails de reset password
    if (strpos($line, 'reset-password') !== false) {
        // Extraer la URL
        if (preg_match('/http:\/\/[^\s"]+reset-password\/[a-zA-Z0-9]+[^\s"]*/', $line, $matches)) {
            $url = $matches[0];
            
            // Limpiar caracteres no deseados al final
            $url = rtrim($url, '",');
            
            echo "🔗 ENLACE DE RECUPERACIÓN ENCONTRADO:\n";
            echo "   $url\n\n";
            
            // Extraer el token
            if (preg_match('/reset-password\/([a-zA-Z0-9]+)/', $url, $tokenMatches)) {
                echo "🎫 Token: {$tokenMatches[1]}\n";
            }
            
            // Extraer el email si está en la URL
            if (preg_match('/email=([^&\s"]+)/', $url, $emailMatches)) {
                echo "📧 Email: " . urldecode($emailMatches[1]) . "\n";
            }
            
            echo "\n" . str_repeat("=", 80) . "\n\n";
            $found = true;
        }
    }
}

if (!$found) {
    echo "❌ No se encontraron enlaces de recuperación en el log.\n";
    echo "💡 Asegúrate de haber solicitado una recuperación de contraseña primero.\n\n";
    echo "📝 Para solicitar una recuperación:\n";
    echo "   1. Ve a: http://127.0.0.1:8000/forgot-password\n";
    echo "   2. Ingresa tu email\n";
    echo "   3. Ejecuta este script nuevamente\n\n";
} else {
    echo "✅ ¡Listo! Copia el enlace de arriba y pégalo en tu navegador.\n\n";
}