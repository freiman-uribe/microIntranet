# 🏢 Micro Intranet - Sistema de Gestión Empresarial

Sistema web integral desarrollado con Laravel para la gestión de usuarios, cotizaciones y notificaciones administrativas en entornos empresariales.

## 📋 Tabla de Contenidos
- [Características](#características)
- [Requisitos del Sistema](#requisitos-del-sistema)
- [Instalación](#instalación)
- [Configuración](#configuración)
- [Uso del Sistema](#uso-del-sistema)
- [Comandos Artisan](#comandos-artisan)
- [Arquitectura](#arquitectura)
- [Base de Datos](#base-de-datos)
- [Funcionalidades](#funcionalidades)
- [Seguridad](#seguridad)
- [Contribución](#contribución)

## 🚀 Características

### 👥 Gestión de Usuarios
- ✅ Sistema de autenticación completo
- ✅ Roles de administrador y usuario regular
- ✅ Perfiles de usuario editables
- ✅ Gestión de usuarios menores de edad (< 18 años)
- ✅ Sistema de recuperación de contraseñas por email
- ✅ Validaciones de seguridad avanzadas

### 📊 Sistema de Cotizaciones
- ✅ Creación y gestión de cotizaciones
- ✅ Catálogo de productos dinámico
- ✅ Cálculos automáticos de totales
- ✅ Exportación a Excel
- ✅ Filtros avanzados por fecha y monto
- ✅ Sistema de permisos por usuario

### 🔔 Sistema de Notificaciones
- ✅ Notificaciones automáticas para usuarios menores
- ✅ Panel de administración de notificaciones
- ✅ Contador en tiempo real de notificaciones sin leer
- ✅ Notificaciones por email
- ✅ Interfaz responsive con dropdown

### 🔒 Características de Seguridad
- ✅ Middleware de autenticación
- ✅ Protección CSRF
- ✅ Validación de permisos por rol
- ✅ Prevención de auto-eliminación de administradores
- ✅ Observer pattern para auditoría

## 💻 Requisitos del Sistema

### Software Requerido
- **PHP**: 8.2 o superior
- **Composer**: 2.8.11 o superior
- **Node.js**: 22.18.0 o superior
- **NPM**: 10.9.3 o superior
- **Base de datos**: SQLite (por defecto) o MySQL

### Extensiones PHP Necesarias
- `php-sqlite3` (para SQLite)
- `php-pdo`
- `php-mbstring`
- `php-xml`
- `php-ctype`
- `php-json`
- `php-tokenizer`
- `php-curl`
- `php-zip`

### Versiones de Framework
- **Laravel Framework**: 12.31.1
- **Bootstrap**: 5.3.0
- **Font Awesome**: 6.4.0
- **Maatwebsite Excel**: 3.1.0
- **Vite**: 7.0.4

## 🛠️ Instalación

### 1. Clonar el Repositorio
```bash
git clone [URL_DEL_REPOSITORIO]
cd microIntranet
```

### 2. Instalar Dependencias PHP
```bash
composer install
```

### 3. Instalar Dependencias JavaScript
```bash
npm install
```

### 4. Configurar Variables de Entorno
```bash
cp .env.example .env
```

### 5. Generar Clave de Aplicación
```bash
php artisan key:generate
```

### 6. Configurar Base de Datos
```bash
# Para SQLite (recomendado para desarrollo)
touch database/database.sqlite

# Para MySQL, editar .env con tus credenciales:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=microintranet
# DB_USERNAME=tu_usuario
# DB_PASSWORD=tu_contraseña
```

### 7. Ejecutar Migraciones
```bash
php artisan migrate
```

### 8. Crear Tabla de Notificaciones
```bash
php artisan notifications:table
php artisan migrate
```

### 9. Compilar Assets
```bash
npm run build
# o para desarrollo:
npm run dev
```

### 10. Crear Enlace Simbólico de Almacenamiento
```bash
php artisan storage:link
```

## ⚙️ Configuración

### Configuración de Email (Opcional)
Para habilitar notificaciones por email, configurar en `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu_email@gmail.com
MAIL_PASSWORD=tu_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tu_email@gmail.com
MAIL_FROM_NAME="Micro Intranet"
```

### Configuración de Aplicación
```env
APP_NAME="Micro Intranet"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
```

## 🎯 Uso del Sistema

### Iniciar Servidor de Desarrollo
```bash
php artisan serve
```
El sistema estará disponible en: `http://127.0.0.1:8000`

### Crear Usuario Administrador (Primera vez)
```bash
php artisan tinker
```
```php
use App\Models\User;
User::create([
    'nombre' => 'Admin',
    'apellido' => 'Sistema',
    'email' => 'admin@microintranet.com',
    'edad' => 30,
    'password' => bcrypt('password123'),
    'admin' => true
]);
```

### Credenciales de Acceso por Defecto
- **Email**: admin@microintranet.com
- **Contraseña**: password123

## 🔧 Comandos Artisan

### Comandos de Notificaciones
```bash
# Mostrar todas las notificaciones del sistema
php artisan notifications:show

# Mostrar solo notificaciones de administradores
php artisan notifications:show --admin-only

# Probar seguridad del sistema de notificaciones
php artisan notifications:test-security
```

### Comandos de Usuarios
```bash
# Probar paginación de usuarios
php artisan users:test-pagination

# Mostrar estadísticas de usuarios menores
php artisan app:show-minor-users-stats
```

### Comandos de Cotizaciones
```bash
# Recalcular totales brutos
php artisan cotizacion:recalculo_bruto
```

### Comandos de Recuperación de Contraseñas
```bash
# Mostrar todos los enlaces de recuperación generados
php artisan reset:show-links

# Ver el último enlace de recuperación enviado
php artisan reset:show-last-link

# Ver enlaces de recuperación en tiempo real (útil para development)
php artisan reset:show-links --watch

# Mostrar solo enlaces activos (no expirados)
php artisan reset:show-links --active

# Limpiar enlaces expirados de recuperación
php artisan auth:clear-resets
```

### Comandos de Testing
```bash
# Probar notificaciones de usuarios menores
php artisan app:test-minor-user-notification
php artisan app:test-minor-user-notifications
```

### Comandos Laravel Estándar
```bash
# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Ver rutas
php artisan route:list

# Crear migraciones
php artisan make:migration create_table_name

# Ejecutar seeders
php artisan db:seed

# Ver estado de migraciones
php artisan migrate:status
```

## 🏗️ Arquitectura

### Estructura del Proyecto
```
microIntranet/
├── app/
│   ├── Console/Commands/          # Comandos Artisan personalizados
│   ├── Contracts/                 # Interfaces y contratos
│   ├── Http/Controllers/          # Controladores
│   ├── Http/Requests/            # Form Requests
│   ├── Models/                   # Modelos Eloquent
│   ├── Notifications/            # Clases de notificaciones
│   ├── Observers/               # Observer patterns
│   ├── Repositories/            # Repositorios de datos
│   └── Services/               # Lógica de negocio
├── database/
│   ├── migrations/             # Migraciones de BD
│   ├── seeders/               # Seeders
│   └── database.sqlite        # Base de datos SQLite
├── resources/
│   ├── views/                 # Vistas Blade
│   ├── css/                   # Estilos CSS
│   └── js/                    # JavaScript
├── routes/
│   └── web.php               # Rutas web
└── storage/
    └── logs/                 # Logs del sistema
```

### Patrones de Diseño Utilizados
- **Repository Pattern**: Para abstracción de datos
- **Service Pattern**: Para lógica de negocio
- **Observer Pattern**: Para eventos de usuario
- **Dependency Injection**: Para inversión de dependencias
- **MVC**: Arquitectura base de Laravel

## 💾 Base de Datos

### Tablas Principales
- **users**: Información de usuarios del sistema
- **cotizacion_c**: Cabeceras de cotizaciones
- **cotizacion_d**: Detalles de cotizaciones
- **productos**: Catálogo de productos
- **notifications**: Sistema de notificaciones
- **cache**: Caché del sistema
- **jobs**: Cola de trabajos

### Migraciones Disponibles
```bash
# Ver estado de todas las migraciones
php artisan migrate:status

# Ejecutar migraciones pendientes
php artisan migrate

# Revertir migraciones
php artisan migrate:rollback

# Refrescar base de datos
php artisan migrate:fresh
```

## 🎨 Funcionalidades

### Panel de Administración
- ✅ Gestión completa de usuarios
- ✅ Visualización de todas las cotizaciones
- ✅ Sistema de notificaciones centralizado
- ✅ Estadísticas y reportes
- ✅ Configuración del sistema

### Panel de Usuario
- ✅ Gestión de perfil personal
- ✅ Creación y edición de cotizaciones propias
- ✅ Visualización de cotizaciones históricas
- ✅ Recuperación de contraseña

### Características Técnicas
- ✅ Interfaz responsive (Bootstrap 5)
- ✅ Paginación personalizada
- ✅ Filtros dinámicos
- ✅ Exportación de datos
- ✅ Validaciones del lado cliente y servidor
- ✅ Notificaciones en tiempo real
- ✅ Sistema de logs estructurado

## 🔒 Seguridad

### Medidas Implementadas
- **Autenticación**: Sistema completo de login/logout
- **Autorización**: Middleware de permisos por rutas
- **Validación**: Form Requests para validación de datos
- **CSRF Protection**: Tokens en todos los formularios
- **XSS Prevention**: Escape automático en vistas Blade
- **SQL Injection**: Uso de Eloquent ORM y Query Builder
- **Password Hashing**: Bcrypt para contraseñas
- **Rate Limiting**: Limitación de intentos de login

### Roles y Permisos
```php
// Administrador puede:
- Ver todos los usuarios
- Crear/editar/eliminar usuarios
- Ver todas las cotizaciones
- Recibir notificaciones de menores
- Acceder a configuración del sistema

// Usuario regular puede:
- Ver/editar su propio perfil
- Crear/editar sus propias cotizaciones
- Cambiar su contraseña
- Solicitar recuperación de contraseña
```

## 🚦 Testing

### Ejecutar Tests
```bash
# Todos los tests
php artisan test

# Tests específicos
php artisan test --filter UserTest

# Con cobertura
php artisan test --coverage
```

### Tests Disponibles
- Unit Tests para modelos
- Feature Tests para controladores
- Tests de integración
- Tests de API

## 📝 Logs y Debugging

### Ubicación de Logs
```bash
# Ver logs en tiempo real
php artisan pail

# Limpiar logs
php artisan log:clear

# Ubicación manual
storage/logs/laravel.log
```

### Debug Mode
Para desarrollo, mantener en `.env`:
```env
APP_DEBUG=true
LOG_LEVEL=debug
```

## 🔄 Mantenimiento

### Comandos de Limpieza
```bash
# Limpiar todo el caché
php artisan optimize:clear

# Optimizar para producción
php artisan optimize

# Limpiar vistas compiladas
php artisan view:clear

# Limpiar configuración
php artisan config:clear
```

### Backup de Base de Datos
```bash
# SQLite
cp database/database.sqlite database/backup_$(date +%Y%m%d_%H%M%S).sqlite

# MySQL
mysqldump -u usuario -p microintranet > backup_$(date +%Y%m%d_%H%M%S).sql
```

## 🌐 Despliegue en Producción

### Preparación
```bash
# Instalar dependencias de producción
composer install --no-dev --optimize-autoloader

# Compilar assets para producción
npm run build

# Configurar caché
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Configurar permisos
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

### Variables de Entorno de Producción
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.com

# Base de datos de producción
DB_CONNECTION=mysql
DB_HOST=tu-host
DB_DATABASE=tu_bd
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña

# Email de producción
MAIL_MAILER=smtp
# ... configuración de email
```

## 🐛 Solución de Problemas

### Errores Comunes

**Error: Class not found**
```bash
composer dump-autoload
```

**Error: Permission denied**
```bash
sudo chmod -R 775 storage/
sudo chmod -R 775 bootstrap/cache/
```

**Error: Key not found**
```bash
php artisan key:generate
```

**Error: Database not found**
```bash
touch database/database.sqlite
php artisan migrate
```

## 📞 Soporte

Para reportar bugs o solicitar características:
1. Crear un issue en el repositorio
2. Proporcionar logs relevantes
3. Incluir pasos para reproducir el problema
4. Especificar versión de PHP y Laravel

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 🤝 Contribución

### Cómo Contribuir
1. Fork del repositorio
2. Crear rama para la feature (`git checkout -b feature/nueva-funcionalidad`)
3. Commit de los cambios (`git commit -am 'Agregar nueva funcionalidad'`)
4. Push a la rama (`git push origin feature/nueva-funcionalidad`)
5. Crear Pull Request

### Estándares de Código
- Seguir PSR-12 para PHP
- Usar camelCase para JavaScript
- Documentar funciones públicas
- Escribir tests para nuevas funcionalidades
- Usar nombres descriptivos para variables y métodos

### Comandos de Desarrollo
```bash
# Verificar estilo de código
./vendor/bin/pint

# Analizar código
php artisan insights

# Ejecutar tests
php artisan test

# Servidor de desarrollo con hot reload
npm run dev
php artisan serve
```

---

**Micro Intranet** - Sistema de Gestión Empresarial  
Desarrollado con ❤️ usando Laravel 12 y Bootstrap 5

**Versión**: 1.0.0  
**Última Actualización**: Septiembre 2025

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
