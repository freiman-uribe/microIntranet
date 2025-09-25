<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña - Micro Intranet</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 40px 30px;
        }
        .btn {
            display: inline-block;
            padding: 15px 30px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
        }
        .btn:hover {
            background: #0056b3;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
        .security-note {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
        }
        .icon {
            font-size: 48px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">🔐</div>
            <h1>Restablecer Contraseña</h1>
            <p>Micro Intranet</p>
        </div>
        
        <div class="content">
            <h2>Hola {{ $user->nombre }},</h2>
            
            <p>Has solicitado restablecer tu contraseña para tu cuenta en <strong>Micro Intranet</strong>.</p>
            
            <p>Para proceder, haz clic en el siguiente botón:</p>
            
            <div style="text-align: center;">
                <a href="{{ $resetUrl }}" class="btn">Restablecer mi Contraseña</a>
            </div>
            
            <div class="security-note">
                <strong>⚠️ Importante:</strong>
                <ul>
                    <li>Este enlace expira en <strong>60 minutos</strong> por seguridad</li>
                    <li>Si no solicitaste este cambio, ignora este email</li>
                    <li>Nunca compartas este enlace con nadie</li>
                </ul>
            </div>
            
            <p>Si el botón no funciona, copia y pega esta URL en tu navegador:</p>
            <p style="word-break: break-all; background: #f8f9fa; padding: 10px; border-radius: 5px; font-family: monospace;">
                {{ $resetUrl }}
            </p>
            
            <hr style="margin: 30px 0;">
            
            <p>Si tienes problemas o no solicitaste este cambio, contacta con el administrador del sistema.</p>
            
            <p>Saludos,<br><strong>Equipo de Micro Intranet</strong></p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} Micro Intranet. Todos los derechos reservados.</p>
            <p>Este es un email automático, por favor no respondas a este mensaje.</p>
        </div>
    </div>
</body>
</html>