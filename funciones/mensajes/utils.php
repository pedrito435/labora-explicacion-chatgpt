<?php
// labora_db/funciones/mensajes/utils.php
require_once __DIR__ . '/../../config/conexion.php';
mysqli_set_charset($conn,'utf8mb4');

/**
 * Detecta el actor logueado (usuario o empleado).
 * Priorizamos EMPLEADO si por error quedaran ambos IDs en sesión.
 */
function ms_current_actor(): ?array {
  // Empleado (prioridad)
  $emp = $_SESSION['empleado_id']
      ?? $_SESSION['id_empleado']
      ?? ($_SESSION['empleado']['id_empleado'] ?? null);
  if (is_numeric($emp) && (int)$emp > 0) {
    return ['tipo' => 'empleado', 'id' => (int)$emp];
  }

  // Usuario
  $usr = $_SESSION['usuario_id']
      ?? $_SESSION['id_usuario']
      ?? $_SESSION['user_id']
      ?? ($_SESSION['usuario']['id_usuario'] ?? ($_SESSION['usuario']['id'] ?? null));
  if (is_numeric($usr) && (int)$usr > 0) {
    return ['tipo' => 'usuario', 'id' => (int)$usr];
  }

  return null;
}

/** Devuelve nombre + iniciales (y foto si mañana la querés usar) */
function ms_persona(mysqli $conn, string $tipo, int $id): array {
  if ($tipo==='empleado') {
    $s = $conn->prepare("SELECT nombre, foto_perfil FROM empleado WHERE id_empleado=?");
  } else {
    $s = $conn->prepare("SELECT nombre, foto_perfil_usuario AS foto_perfil FROM usuarios WHERE id_usuario=?");
  }
  $s->bind_param('i', $id);
  $s->execute();
  $r   = $s->get_result();
  $row = $r ? $r->fetch_assoc() : null;
  $s->close();

  $nombre = $row['nombre'] ?? (($tipo==='empleado'?'Empleado ':'Usuario ').'#'.$id);
  $inic   = strtoupper(mb_substr(trim($nombre), 0, 1, 'UTF-8') ?: '?');
  return ['nombre'=>$nombre, 'iniciales'=>$inic, 'foto'=>$row['foto_perfil'] ?? null];
}
