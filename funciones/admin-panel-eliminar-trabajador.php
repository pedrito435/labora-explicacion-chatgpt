<?php
// labora_db/funciones/admin-panel-eliminar-trabajador.php
session_start();
if (empty($_SESSION['admin'])) {
  header("Location: /labora_db/vistas/admin/admin-login.php");
  exit();
}
require_once __DIR__ . '/../config/conexion.php';
mysqli_set_charset($conn, 'utf8mb4');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
  header("Location: /labora_db/vistas/admin/admin-panel.php#trabajadores");
  exit();
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
  $conn->begin_transaction();

  // 0) Obtener paths antes de borrar
  $stmt = $conn->prepare("SELECT dni_frente_path, dni_dorso_path, matricula_path FROM empleado WHERE id_empleado = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $paths = $stmt->get_result()->fetch_assoc();

  // 1) Borrar dependencias conocidas del trabajador (ajustar a tu schema)
  // Valoraciones hechas a este empleado
  $stmt = $conn->prepare("DELETE FROM valoraciones WHERE id_empleado = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();

  // Experiencia, educación, trabajos publicados, etc. (si existen)
  // $stmt = $conn->prepare("DELETE FROM experiencia_laboral WHERE id_empleado = ?");
  // $stmt->bind_param("i", $id);
  // $stmt->execute();
  // $stmt = $conn->prepare("DELETE FROM educacion WHERE id_empleado = ?");
  // $stmt->bind_param("i", $id);
  // $stmt->execute();
  // $stmt = $conn->prepare("DELETE FROM trabajos WHERE id_empleado = ?");
  // $stmt->bind_param("i", $id);
  // $stmt->execute();
  // $stmt = $conn->prepare("DELETE FROM fotos_trabajo WHERE id_empleado = ?");
  // $stmt->bind_param("i", $id);
  // $stmt->execute();

  // Chats donde el empleado participe (depende tu modelo)
  // $stmt = $conn->prepare("DELETE FROM chat_participante WHERE id_empleado = ?");
  // $stmt->bind_param("i", $id);
  // $stmt->execute();
  // $stmt = $conn->prepare("DELETE cm FROM chat_mensaje cm
  //                         JOIN chat_conversacion cc ON cc.id_conversacion = cm.id_conversacion
  //                         WHERE cc.id_empleado = ?");
  // $stmt->bind_param("i", $id);
  // $stmt->execute();
  // $stmt = $conn->prepare("DELETE FROM chat_conversacion WHERE id_empleado = ?");
  // $stmt->bind_param("i", $id);
  // $stmt->execute();

  // 2) Borrar fila padre (empleado)
  $del = $conn->prepare("DELETE FROM empleado WHERE id_empleado = ?");
  $del->bind_param("i", $id);
  $del->execute();

  // 3) Eliminar carpeta de verificaciones tipo /uploads/verificaciones/empleado_{id}/
  if ($paths) {
    $any = $paths['dni_frente_path'] ?? $paths['dni_dorso_path'] ?? $paths['matricula_path'] ?? null;
    if ($any) {
      $any = str_replace('\\','/',$any);
      if (preg_match('#^uploads/verificaciones/(empleado_\d+)/#', $any, $m)) {
        $folderRel = 'uploads/verificaciones/' . $m[1];
        $projectRoot = realpath(__DIR__ . '/..'); if ($projectRoot === false) { $projectRoot = dirname(__DIR__); }
        $folderAbs = $projectRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $folderRel);
        $verifBase = realpath($projectRoot . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'verificaciones');
        $folderReal = realpath($folderAbs);

        if ($verifBase && $folderReal && strpos($folderReal, $verifBase) === 0) {
          $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($folderReal, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
          );
          foreach ($it as $file) {
            if ($file->isDir()) { @rmdir($file->getPathname()); }
            else { @unlink($file->getPathname()); }
          }
          @rmdir($folderReal);
        }
      }
    }
  }

  $conn->commit();
  header("Location: /labora_db/vistas/admin/admin-panel.php#trabajadores");
  exit();

} catch (Throwable $e) {
  $conn->rollback();
  http_response_code(500);
  echo "Error al eliminar trabajador: " . htmlspecialchars($e->getMessage());
}
