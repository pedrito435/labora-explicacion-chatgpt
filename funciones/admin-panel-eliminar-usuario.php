<?php
// labora_db/funciones/admin-panel-eliminar-usuario.php
session_start();
if (empty($_SESSION['admin'])) {
  header("Location: /labora_db/vistas/admin/admin-login.php");
  exit();
}
require_once __DIR__ . '/../config/conexion.php';
mysqli_set_charset($conn, 'utf8mb4');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
  header("Location: /labora_db/vistas/admin/admin-panel.php#usuarios");
  exit();
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
  $conn->begin_transaction();

  // 1) Borrar dependencias conocidas del usuario (ajustar según tu schema)
  // Ejemplos comunes:
  $stmt = $conn->prepare("DELETE FROM valoraciones WHERE id_usuario = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();

  // Si tenés favoritos de usuarios a trabajadores, por ejemplo:
  // $stmt = $conn->prepare("DELETE FROM favoritos WHERE id_usuario = ?");
  // $stmt->bind_param("i", $id);
  // $stmt->execute();

  // Si tenés chats (depende tu modelo):
  // Primero relacion/participación y luego mensajes/conversaciones si corresponde
  // $stmt = $conn->prepare("DELETE FROM chat_participante WHERE id_usuario = ?");
  // $stmt->bind_param("i", $id);
  // $stmt->execute();
  // $stmt = $conn->prepare("DELETE cm FROM chat_mensaje cm
  //                         JOIN chat_conversacion cc ON cc.id_conversacion = cm.id_conversacion
  //                         WHERE cc.id_usuario = ?");
  // $stmt->bind_param("i", $id);
  // $stmt->execute();
  // $stmt = $conn->prepare("DELETE FROM chat_conversacion WHERE id_usuario = ?");
  // $stmt->bind_param("i", $id);
  // $stmt->execute();

  // Si hay reservas/pedidos hechos por usuario:
  // $stmt = $conn->prepare("DELETE FROM reservas WHERE id_usuario = ?");
  // $stmt->bind_param("i", $id);
  // $stmt->execute();

  // 2) Obtener paths para eliminar archivos del usuario (si los hubiera)
  $paths = null;
  if ($st = $conn->prepare("SELECT dni_frente_path, dni_dorso_path, matricula_path FROM usuarios WHERE id_usuario = ?")) {
    $st->bind_param("i", $id);
    $st->execute();
    $paths = $st->get_result()->fetch_assoc();
  }

  // 3) Borrar usuario (padre)
  $del = $conn->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
  $del->bind_param("i", $id);
  $del->execute();

  // 4) Limpiar carpeta de verificaciones si seguís ese esquema de /uploads/verificaciones/usuario_{id}/
  if ($paths) {
    $any = $paths['dni_frente_path'] ?? $paths['dni_dorso_path'] ?? $paths['matricula_path'] ?? null;
    if ($any) {
      $any = str_replace('\\','/',$any);
      if (preg_match('#^uploads/verificaciones/(usuario_\d+)/#', $any, $m)) {
        $folderRel = 'uploads/verificaciones/' . $m[1];
        $projectRoot = realpath(__DIR__ . '/..'); if ($projectRoot === false) { $projectRoot = dirname(__DIR__); }
        $folderAbs = $projectRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $folderRel);
        $verifBase = realpath($projectRoot . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'verificaciones');
        $folderReal = realpath($folderAbs);

        if ($verifBase && $folderReal && strpos($folderReal, $verifBase) === 0) {
          // rrmdir recursivo
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
  header("Location: /labora_db/vistas/admin/admin-panel.php#usuarios");
  exit();

} catch (Throwable $e) {
  $conn->rollback();
  http_response_code(500);
  echo "Error al eliminar usuario: " . htmlspecialchars($e->getMessage());
}
