<?php
// labora_db/funciones/buscar.php

ini_set('display_errors', 0);
error_reporting(E_ALL);
header('Content-Type: application/json; charset=utf-8');

session_start();
require '../config/conexion.php';
mysqli_set_charset($conn, 'utf8mb4');

$DEBUG = isset($_GET['debug']) && $_GET['debug'] == '1';

/** Detecta el ID del usuario a partir de varias claves de sesión posibles. */
function current_user_id(): int {
    $candidates = [
        $_SESSION['id_usuario'] ?? null,
        $_SESSION['user_id'] ?? null,
        $_SESSION['usuario_id'] ?? null,
        $_SESSION['usuario']['id_usuario'] ?? null,
        $_SESSION['usuario']['id'] ?? null,
    ];
    foreach ($candidates as $v) {
        if (is_numeric($v) && (int)$v > 0) return (int)$v;
    }
    return 0;
}

/** BASE URL (ej: /labora_db) para normalizar rutas de imagen */
function base_url_root(): string {
    $parts = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
    return '/' . ($parts[0] ?? '');
}

/** Normaliza la foto guardada en BD a una URL servible */
function foto_bd_a_url(?string $v, string $BASE_URL, string $default): string {
    $v = trim((string)$v);
    if ($v === '') return $default;
    if (preg_match('#^https?://#i', $v)) return $v;          // URL completa
    if (strpos($v, '/') === 0) return $v;                    // Ruta absoluta web
    if (strpos($v, '/') !== false) {                         // Carpeta + archivo
        return rtrim($BASE_URL, '/') . '/' . ltrim($v, '/');
    }
    return rtrim($BASE_URL, '/') . '/uploads/' . $v;         // Solo archivo
}

/** Chequea si existe una columna en una tabla */
function column_exists(mysqli $conn, string $table, string $column): bool {
    $t = $conn->real_escape_string($table);
    $c = $conn->real_escape_string($column);
    $sql = "SHOW COLUMNS FROM `$t` LIKE '$c'";
    $res = $conn->query($sql);
    if ($res === false) return false;
    $ok = $res->num_rows > 0;
    $res->free();
    return $ok;
}

// ===== Gate: sólo usuarios APROBADOS =====
$userId = current_user_id();
if ($userId <= 0) { echo json_encode([]); exit; }

$gateOk = false;
$stmtGate = $conn->prepare("SELECT estado_verificacion FROM usuarios WHERE id_usuario = ?");
if ($stmtGate) {
    $stmtGate->bind_param("i", $userId);
    if ($stmtGate->execute()) {
        $resGate = $stmtGate->get_result();
        $rowGate = $resGate ? $resGate->fetch_assoc() : null;
        if ($rowGate && ($rowGate['estado_verificacion'] ?? '') === 'aprobado') $gateOk = true;
    }
    $stmtGate->close();
}
if (!$gateOk) { echo json_encode([]); exit; }

// ===== Filtros =====
$BASE_URL        = base_url_root();                          // ej: /labora_db
$DEFAULT_IMG_URL = $BASE_URL . '/imagenes/default_user.jpg';

$zona      = isset($_GET['zona'])      ? trim((string)$_GET['zona'])      : '';
$profesion = isset($_GET['profesion']) ? trim((string)$_GET['profesion']) : '';
$busqueda  = isset($_GET['busqueda'])  ? trim((string)$_GET['busqueda'])  : '';
$orden     = isset($_GET['orden'])     ? trim((string)$_GET['orden'])     : '';

// ===== Detectar una columna de fecha si existe =====
$dateCandidates = ['fecha_creacion','fecha_alta','fecha_registro','creado_en','created_at','fecha','fecha_verificacion'];
$dateCol = null;
foreach ($dateCandidates as $cand) {
    if (column_exists($conn, 'empleado', $cand)) { $dateCol = $cand; break; }
}

// ===== Consulta: sólo empleados APROBADOS + datos del plan =====
$sql = "SELECT 
            e.id_empleado,
            e.nombre,
            e.profesion,
            e.zona_trabajo,
            e.descripcion_servicios,
            e.foto_perfil AS foto_bd,
            p.search_priority,
            p.badge        AS plan_badge,
            p.frame_style  AS plan_frame,
            p.portfolio_media_limit
        " . ($dateCol ? ", e.`$dateCol` AS fecha_row" : "") . "
        FROM empleado e
        LEFT JOIN plans p ON p.id = e.plan_id
        WHERE e.estado_verificacion = 'aprobado'";

$params = [];
$types  = '';

if ($zona !== '') {
    $sql .= " AND e.zona_trabajo = ?";
    $params[] = $zona;
    $types    .= 's';
}
if ($profesion !== '') {
    $sql .= " AND e.profesion = ?";
    $params[] = $profesion;
    $types    .= 's';
}
if ($busqueda !== '') {
    $sql .= " AND (e.nombre LIKE ? OR e.profesion LIKE ? OR e.descripcion_servicios LIKE ?)";
    $like = "%$busqueda%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types    .= 'sss';
}

/** ORDER BY con prioridad de plan */
$orderby = "COALESCE(p.search_priority,0) DESC, e.id_empleado DESC";
switch (strtolower($orden)) {
    case 'recientes':
        $orderby = "COALESCE(p.search_priority,0) DESC, " . ($dateCol ? "e.`$dateCol` DESC, " : "") . "e.id_empleado DESC";
        break;
    case 'nombre_asc':
        $orderby = "COALESCE(p.search_priority,0) DESC, e.nombre ASC, e.id_empleado DESC";
        break;
    case 'nombre_desc':
        $orderby = "COALESCE(p.search_priority,0) DESC, e.nombre DESC, e.id_empleado DESC";
        break;
}
$sql .= " ORDER BY $orderby";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    $out = ['error' => 'Error preparando consulta', 'detalle' => $conn->error, 'sql' => $DEBUG ? $sql : null];
    echo json_encode($out, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}
if ($types !== '') { $stmt->bind_param($types, ...$params); }
if (!$stmt->execute()) {
    $out = ['error' => 'Error ejecutando consulta', 'detalle' => $stmt->error, 'sql' => $DEBUG ? $sql : null];
    echo json_encode($out, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

$res = $stmt->get_result();
$trabajadores = [];
while ($fila = $res->fetch_assoc()) {
    $fila['foto'] = foto_bd_a_url($fila['foto_bd'] ?? '', $BASE_URL, $DEFAULT_IMG_URL);
    unset($fila['foto_bd']);
    $fila['plan_badge'] = $fila['plan_badge'] ?? null;
    $fila['plan_frame'] = $fila['plan_frame'] ?? null;
    $fila['search_priority'] = (int)($fila['search_priority'] ?? 0);
    $trabajadores[] = $fila;
}

echo json_encode($trabajadores, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
