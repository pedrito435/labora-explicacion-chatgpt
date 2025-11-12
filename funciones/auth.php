<?php
// labora_db/funciones/auth.php
function auth_no_cache(): void {
    // iniciar sesión si no está activa
    if (session_status() === PHP_SESSION_NONE) {
        // Opcional: ajustes de cookie
        ini_set('session.cookie_path', '/');
        ini_set('session.cookie_samesite', 'Lax');
        // en local http: ini_set('session.cookie_secure','0'); en prod https: '1'

        session_start();
    }
    // Enviar headers no-cache (idempotentes)
    if (!headers_sent()) {
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Expires: 0');
    }
}

function auth_require_usuario(string $redirect = '/labora_db/formularios/login-options.html'): void {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (empty($_SESSION['usuario_id'])) {
        header("Location: {$redirect}");
        exit();
    }
}

function auth_require_empleado(string $redirect = '/labora_db/formularios/login-options.html'): void {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (empty($_SESSION['empleado_id'])) {
        header("Location: {$redirect}");
        exit();
    }
}

function auth_require_admin(string $redirect = '/labora_db/vistas/admin/admin-login.php'): void {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (empty($_SESSION['admin_id'])) { // ajustá si usás otro nombre
        header("Location: {$redirect}");
        exit();
    }
}

// 30 minutos por defecto
function auth_activity_timeout(int $seconds = 1800, string $redirect = '/labora_db/formularios/login-options.html'): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $now = time();

    // Si pasó el tiempo de inactividad máximo → cerrar sesión y redirigir
    if (isset($_SESSION['__last_activity']) && ($now - (int)$_SESSION['__last_activity']) > $seconds) {
        // limpiar sesión
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
        if (!headers_sent()) header("Location: {$redirect}");
        exit();
    }

    // actualizar timestamp de actividad
    $_SESSION['__last_activity'] = $now;
}
