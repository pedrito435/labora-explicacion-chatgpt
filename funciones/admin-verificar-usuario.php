<?php
// labora_db/funciones/admin-verificar-usuario.php
session_start();
if (empty($_SESSION['admin'])) { header("Location: /labora_db/vistas/admin/admin-login.php"); exit(); }

require_once __DIR__ . '/../config/conexion.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // errores como excepciones

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once __DIR__ . '/../PHPMailer-master/src/Exception.php';
require_once __DIR__ . '/../PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer-master/src/SMTP.php';

function enviarDecisionUsuario($correo, $nombre, $accion, $obs) {
  if (empty($correo)) return;
  $mail = new PHPMailer(true);
  try {
    //<---------------------------------------------------------------------------------------------->
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'labora1357@gmail.com';
    $mail->Password = 'efrx dujz cwyw jtsj';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->setFrom('labora1357@gmail.com', 'Labora');
    $mail->addAddress($correo, $nombre ?: $correo);
    $mail->isHTML(true);

    // Opcional: asegurá el charset del cuerpo
$mail->CharSet  = 'UTF-8';
$mail->Encoding = 'base64';

if ($accion === 'aprobar') {
  // ⬅️ SUBJECT *sin acentos/emoji*
  $mail->Subject = 'LABORA | Verificacion aprobada';

  // Preheader (oculto) + cuerpo lindo
  $mail->Body = "
  <div style='display:none;max-height:0;overflow:hidden;opacity:0;color:transparent'>
    Tu verificación fue aprobada. Ya podés usar todas las funciones de LABORA.
  </div>
  <div style='font-family:Arial,sans-serif;line-height:1.6;color:#1e293b;background:#f5f7fb;padding:24px'>
    <div style='max-width:640px;margin:0 auto;background:#ffffff;border-radius:14px;overflow:hidden;border:1px solid #e5e7eb'>
      <div style='background:#005f8c;height:6px;width:100%'></div>
      <div style='text-align:center;padding:28px 20px 10px'>
        <img src='https://i.imgur.com/7rI0XwP.png' alt='Labora' style='max-width:120px'>
      </div>
      <div style='padding:8px 28px 28px'>
        <h2 style='margin:8px 0 12px;font-size:24px;color:#0f172a'>
          ¡Felicitaciones, ".htmlspecialchars($nombre,ENT_QUOTES,'UTF-8')."!
        </h2>
        <div style='margin:12px 0 20px;padding:12px 14px;border:1px solid #d1fae5;background:#ecfdf5;border-radius:10px;font-size:15px'>
          <strong style='color:#065f46'>Verificación aprobada</strong><br>
          Tu cuenta fue verificada correctamente. Ya estás habilitado/a para contactar y gestionar servicios en LABORA.
        </div>

        <p style='margin:0 0 14px;font-size:15px'>
          ¿Qué sigue?
        </p>
        <ul style='margin:0 0 20px 18px;padding:0;font-size:15px'>
          <li>Completá tu perfil para mejorar la visibilidad.</li>
          <li>Configurá tus preferencias de contacto.</li>
          <li>Explorá trabajadores y guardá tus favoritos.</li>
        </ul>

        <div style='text-align:center;margin-top:22px'>
          <a href='https://labora.com' style='display:inline-block;background:#005f8c;color:#fff;text-decoration:none;padding:12px 22px;border-radius:10px;font-weight:600'>
            Ir a LABORA
          </a>
        </div>

        <hr style='border:none;border-top:1px solid #e5e7eb;margin:24px 0'>

        <p style='font-size:12px;color:#64748b;margin:0;text-align:center'>
          Este es un correo automático. Si no reconocés esta acción, ignorá este mensaje.
        </p>
      </div>
    </div>
  </div>";
} else {
  // ⬅️ SUBJECT *sin acentos/emoji*
  $mail->Subject = 'LABORA | Verificacion rechazada';

  // armamos el bloque de motivo solo si hay texto
  $motivo = trim($obs) !== '' ? "
    <div style='margin:10px 0 16px;padding:12px 14px;border:1px solid #fee2e2;background:#fef2f2;border-radius:10px;font-size:15px'>
      <strong style='color:#991b1b'>Motivo</strong><br>"
      . nl2br(htmlspecialchars($obs,ENT_QUOTES,'UTF-8')) .
    "</div>" : "";

  $mail->Body = "
  <div style='display:none;max-height:0;overflow:hidden;opacity:0;color:transparent'>
    Tu verificación fue rechazada. Revisá la documentación y volvé a intentarlo.
  </div>
  <div style='font-family:Arial,sans-serif;line-height:1.6;color:#1e293b;background:#f5f7fb;padding:24px'>
    <div style='max-width:640px;margin:0 auto;background:#ffffff;border-radius:14px;overflow:hidden;border:1px solid #e5e7eb'>
      <div style='background:#d98324;height:6px;width:100%'></div>
      <div style='text-align:center;padding:28px 20px 10px'>
        <img src='https://i.imgur.com/7rI0XwP.png' alt='Labora' style='max-width:120px'>
      </div>
      <div style='padding:8px 28px 28px'>
        <h2 style='margin:8px 0 12px;font-size:22px;color:#0f172a'>
          Hola, ".htmlspecialchars($nombre,ENT_QUOTES,'UTF-8')."
        </h2>

        <p style='margin:0 0 10px;font-size:15px'>
          Tu solicitud de verificación <strong style='color:#b91c1c'>no pudo aprobarse</strong> por el momento.
        </p>

        ".$motivo."

        <p style='margin:14px 0 10px;font-size:15px'>Para volver a intentarlo, te sugerimos:</p>
        <ul style='margin:0 0 20px 18px;padding:0;font-size:15px'>
          <li>Subir fotos legibles del DNI (frente y dorso).</li>
          <li>Verificar que la matrícula/certificado sea visible y vigente.</li>
          <li>Revisar que tus datos personales coincidan con los documentos.</li>
        </ul>

        <div style='text-align:center;margin-top:22px'>
          <a href='https://labora.com' style='display:inline-block;background:#d98324;color:#fff;text-decoration:none;padding:12px 22px;border-radius:10px;font-weight:600'>
            Reintentar verificacion
          </a>
        </div>

        <hr style='border:none;border-top:1px solid #e5e7eb;margin:24px 0'>

        <p style='font-size:12px;color:#64748b;margin:0;text-align:center'>
          Este es un correo automático. Si tenés dudas, escribinos desde la sección de ayuda dentro de la plataforma.
        </p>
      </div>
    </div>
  </div>";
}

    $mail->send();
  } catch (Exception $e) {
    error_log('[ADMIN USER MAIL ERROR] '.$mail->ErrorInfo);
  }
}

function tableExists(mysqli $conn, string $name): bool {
  $q = $conn->prepare("SELECT COUNT(*) c FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?");
  $q->bind_param("s", $name);
  $q->execute();
  $r = $q->get_result()->fetch_assoc();
  $q->close();
  return !empty($r) && (int)$r['c'] > 0;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header("Location: /labora_db/vistas/admin/admin-panel.php#usuarios-pendientes"); exit();
}

$id  = (int)($_POST['id_usuario'] ?? 0);
$acc = $_POST['accion'] ?? '';
$obs = trim($_POST['observaciones'] ?? '');

if ($id <= 0 || !in_array($acc, ['aprobar','rechazar','pendiente'], true)) {
  header("Location: /labora_db/vistas/admin/admin-panel.php#usuarios-pendientes"); exit();
}
if ($acc === 'rechazar' && $obs === '') {
  header("Location: /labora_db/funciones/admin-usuario.php?id={$id}&err=obs_required"); exit();
}

// Datos para mail + borrar archivos
$stmtInfo = $conn->prepare("
  SELECT nombre, correo,
         foto_perfil_usuario, dni_frente_path, dni_dorso_path, matricula_path
  FROM usuarios WHERE id_usuario = ?
");
$stmtInfo->bind_param("i", $id);
$stmtInfo->execute();
$u = $stmtInfo->get_result()->fetch_assoc();
$stmtInfo->close();

$admin_id = (int)($_SESSION['admin_id'] ?? 0);

if ($acc === 'rechazar') {
  try {
    $conn->begin_transaction();

    // 1) Borrados de tablas relacionadas (según tu dump)
    if (tableExists($conn, 'registro_pendiente_usuarios')) {
      $st = $conn->prepare("DELETE FROM registro_pendiente_usuarios WHERE id_usuario = ?");
      $st->bind_param("i", $id); $st->execute(); $st->close();
    }

    if (tableExists($conn, 'mensajes')) {
      $st = $conn->prepare("
        DELETE FROM mensajes
        WHERE (emisor_tipo='usuario' AND emisor_id=?)
           OR (receptor_tipo='usuario' AND receptor_id=?)
      ");
      $st->bind_param("ii", $id, $id); $st->execute(); $st->close();
    }

    if (tableExists($conn, 'valoraciones')) {
      // Necesario porque tu FK no tiene ON DELETE CASCADE
      $st = $conn->prepare("DELETE FROM valoraciones WHERE id_usuario = ?");
      $st->bind_param("i", $id); $st->execute(); $st->close();
    }

    // 2) Borrar archivos
    $projectRoot = realpath(__DIR__ . '/..');         // labora_db/
    $uploadsRoot = $projectRoot . DIRECTORY_SEPARATOR . 'uploads';

    if ($u && $projectRoot && is_dir($projectRoot)) {
      $candidatos = [
        $u['foto_perfil_usuario'] ?? null,
        $u['dni_frente_path'] ?? null,
        $u['dni_dorso_path'] ?? null,
        $u['matricula_path'] ?? null,
      ];
      foreach ($candidatos as $rel) {
        if (!$rel) continue;
        // Ignorar absolutos/externos
        if (preg_match('~^https?://~i', $rel) || str_starts_with($rel, '/')) continue;

        // Si empieza con "uploads/", ruta relativa a raíz del proyecto
        if (str_starts_with($rel, 'uploads/')) {
          $abs = realpath($projectRoot . DIRECTORY_SEPARATOR . $rel) ?: ($projectRoot . DIRECTORY_SEPARATOR . $rel);
        } else {
          // filename dentro de /uploads
          $abs = realpath($uploadsRoot . DIRECTORY_SEPARATOR . $rel) ?: ($uploadsRoot . DIRECTORY_SEPARATOR . $rel);
        }

        // Evitar traversal y borrar
        if (str_starts_with($abs, $projectRoot) && is_file($abs)) {
          @unlink($abs);
        }
      }
    }

    // 3) Borrar usuario
    $del = $conn->prepare("DELETE FROM usuarios WHERE id_usuario = ? LIMIT 1");
    $del->bind_param("i", $id);
    $del->execute();
    $del->close();

    $conn->commit();

  } catch (Throwable $e) {
    $conn->rollback();
    error_log('[ADMIN USER DELETE ERROR] '.$e->getMessage());
    header("Location: /labora_db/vistas/admin/admin-panel.php?err=del_fail#usuarios-pendientes");
    exit();
  }

  // Mail de rechazo (con datos recuperados antes)
  if ($u) { enviarDecisionUsuario($u['correo'] ?? '', $u['nombre'] ?? '', 'rechazar', $obs); }

  header("Location: /labora_db/vistas/admin/admin-panel.php#usuarios-pendientes");
  exit();
}

// === aprobar / pendiente → UPDATE
$nuevo = $acc === 'aprobar' ? 'aprobado' : 'pendiente';
$stmt = $conn->prepare("
  UPDATE usuarios
     SET estado_verificacion = ?, verificado_por = ?, fecha_verificacion = NOW(), observaciones_verificacion = ?
   WHERE id_usuario = ?
");
$stmt->bind_param("sisi", $nuevo, $admin_id, $obs, $id);
$stmt->execute();
$stmt->close();

if ($u) { enviarDecisionUsuario($u['correo'] ?? '', $u['nombre'] ?? '', $acc, $obs); }

header("Location: /labora_db/vistas/admin/admin-panel.php#usuarios-pendientes");
exit();
