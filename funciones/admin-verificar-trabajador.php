<?php
// labora_db/funciones/admin-verificar-trabajador.php
session_start();
if (empty($_SESSION['admin'])) { header("Location: ../vistas/admin/admin-login.php"); exit(); }

require_once __DIR__ . '/../config/conexion.php';
if (function_exists('mysqli_set_charset')) { @mysqli_set_charset($conn, 'utf8mb4'); }
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // errores como excepciones

// === Email (PHPMailer) ===
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once __DIR__ . '/../PHPMailer-master/src/Exception.php';
require_once __DIR__ . '/../PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer-master/src/SMTP.php';

// Base URL del proyecto (ej: /labora_db)
function base_url_root(): string {
  $parts = explode('/', trim($_SERVER['SCRIPT_NAME'], '/')); // ej: labora_db/funciones/...
  return '/' . ($parts[0] ?? '');
}
$BASE_URL = base_url_root(); // -> /labora_db

function tableExists(mysqli $conn, string $name): bool {
  $q = $conn->prepare("SELECT COUNT(*) c FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?");
  $q->bind_param("s", $name);
  $q->execute();
  $r = $q->get_result()->fetch_assoc();
  $q->close();
  return !empty($r) && (int)$r['c'] > 0;
}

function enviarDecisionTrab($correo, $nombre, $accion, $obs) {
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
    $mail->CharSet  = 'UTF-8';
    $mail->Encoding = 'base64';

    if ($accion === 'aprobar') {
      $mail->Subject = 'LABORA | Verificacion aprobada';
      $mail->Body = "
      <div style='display:none;max-height:0;overflow:hidden;opacity:0;color:transparent'>
        Tu verificacion fue aprobada. Tu perfil ya puede mostrarse en las busquedas.
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
              <strong style='color:#065f46'>Verificacion aprobada</strong><br>
              Tu cuenta fue verificada. Ya estas habilitado/a para difundir tus servicios en LABORA.
            </div>
            <ul style='margin:0 0 20px 18px;padding:0;font-size:15px'>
              <li>Actualiza tu perfil y agrega trabajos/portafolio.</li>
              <li>Configura tu zona de trabajo y metodos de contacto.</li>
              <li>Respondé mensajes para ganar posicionamiento.</li>
            </ul>
            <div style='text-align:center;margin-top:22px'>
              <a href='http://localhost/labora_db/' style='display:inline-block;background:#005f8c;color:#fff;text-decoration:none;padding:12px 22px;border-radius:10px;font-weight:600'>
                Ir a LABORA
              </a>
            </div>
            <hr style='border:none;border-top:1px solid #e5e7eb;margin:24px 0'>
            <p style='font-size:12px;color:#64748b;margin:0;text-align:center'>
              Este es un correo automatico. Si no reconoces esta accion, ignora este mensaje.
            </p>
          </div>
        </div>
      </div>";
    } else {
      $mail->Subject = 'LABORA | Verificacion rechazada';
      $motivo = trim($obs) !== '' ? "
        <div style='margin:10px 0 16px;padding:12px 14px;border:1px solid #fee2e2;background:#fef2f2;border-radius:10px;font-size:15px'>
          <strong style='color:#991b1b'>Motivo</strong><br>".nl2br(htmlspecialchars($obs,ENT_QUOTES,'UTF-8'))."
        </div>" : "";
      $mail->Body = "
      <div style='display:none;max-height:0;overflow:hidden;opacity:0;color:transparent'>
        Tu verificacion fue rechazada. Revisa la documentacion y volve a intentarlo.
      </div>
      <div style='font-family:Arial,sans-serif;line-height:1.6;color:#1e293b;background:#f5f7fb;padding:24px'>
        <div style='max-width:640px;margin:0 auto;background:#ffffff;border-radius:14px;overflow:hidden;border:1px solid #e5e7eb'>
          <div style='background:#d98324;height:6px;width:100%'></div>
          <div style='text-align:center;padding:28px 20px 10px'>
            <img src='https://i.imgur.com/7rI0XwP.png' alt='Labora' style='max-width:120px'>
          </div>
          <div style='padding:8px 28px 28px'>
            <h2 style='margin:8px 0 12px;font-size:22px;color:#0f172a'>Hola, ".htmlspecialchars($nombre,ENT_QUOTES,'UTF-8')."</h2>
            <p style='margin:0 0 10px;font-size:15px'>
              Tu solicitud de verificacion <strong style='color:#b91c1c'>no pudo aprobarse</strong> por el momento.
            </p>
            ".$motivo."
            <p style='margin:14px 0 10px;font-size:15px'>Para reintentar:</p>
            <ul style='margin:0 0 20px 18px;padding:0;font-size:15px'>
              <li>Subi fotos legibles del DNI (frente y dorso).</li>
              <li>Verificá que la matricula/certificado sea visible y vigente.</li>
              <li>Revisá que tus datos coincidan con los documentos.</li>
            </ul>
            <div style='text-align:center;margin-top:22px'>
              <a href='http://localhost/labora_db/' style='display:inline-block;background:#d98324;color:#fff;text-decoration:none;padding:12px 22px;border-radius:10px;font-weight:600'>
                Reintentar verificacion
              </a>
            </div>
            <hr style='border:none;border-top:1px solid #e5e7eb;margin:24px 0'>
            <p style='font-size:12px;color:#64748b;margin:0;text-align:center'>
              Este es un correo automatico. Si tenes dudas, escribinos desde Ayuda en la plataforma.
            </p>
          </div>
        </div>
      </div>";
    }
    $mail->send();
  } catch (Exception $e) {
    error_log('[ADMIN MAIL ERROR] '.$mail->ErrorInfo);
  }
}

// === Handler ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id     = (int)($_POST['id_empleado'] ?? 0);
  $accion = $_POST['accion'] ?? ''; // aprobar | rechazar | pendiente
  $obs    = trim($_POST['observaciones'] ?? '');
  $profesion          = trim($_POST['profesion'] ?? '');
  $titulo_profesional = trim($_POST['titulo_profesional'] ?? '');

  if ($id <= 0 || !in_array($accion, ['aprobar','rechazar','pendiente'], true)) {
    header("Location: {$BASE_URL}/vistas/admin/admin-panel.php#trabajadores"); exit();
  }
  if ($accion === 'rechazar' && $obs === '') {
    header("Location: {$BASE_URL}/funciones/admin-trabajador.php?id={$id}&err=obs_required"); exit();
  }
  if ($accion === 'aprobar' && $profesion === '') {
    header("Location: {$BASE_URL}/funciones/admin-trabajador.php?id={$id}&err=prof_required"); exit();
  }

  // Traigo datos antes de borrar (para email y borrar archivos)
  $stmtInfo = $conn->prepare("
    SELECT nombre, correo, foto_perfil, dni_frente_path, dni_dorso_path, matricula_path
    FROM empleado WHERE id_empleado = ?
  ");
  $stmtInfo->bind_param("i", $id);
  $stmtInfo->execute();
  $emp = $stmtInfo->get_result()->fetch_assoc();
  $stmtInfo->close();

  $admin_id = isset($_SESSION['admin_id']) ? (int)$_SESSION['admin_id'] : null;

  if ($accion === 'rechazar') {
    try {
      $conn->begin_transaction();

      // 1) Borrados auxiliares (segun tu dump)
      if (tableExists($conn, 'registro_pendiente_empleados')) {
        $st = $conn->prepare("DELETE FROM registro_pendiente_empleados WHERE id_empleado = ?");
        $st->bind_param("i", $id); $st->execute(); $st->close();
      }

      if (tableExists($conn, 'mensajes')) {
        $st = $conn->prepare("
          DELETE FROM mensajes
          WHERE (emisor_tipo='empleado' AND emisor_id=?)
             OR (receptor_tipo='empleado' AND receptor_id=?)
        ");
        $st->bind_param("ii", $id, $id); $st->execute(); $st->close();
      }

      if (tableExists($conn, 'valoraciones')) {
        // tu FK no tiene ON DELETE CASCADE -> borrar primero
        $st = $conn->prepare("DELETE FROM valoraciones WHERE id_empleado = ?");
        $st->bind_param("i", $id); $st->execute(); $st->close();
      }

      // Tablas opcionales si existen
      if (tableExists($conn, 'chat_conversacion')) {
        $st = $conn->prepare("
          DELETE FROM chat_conversacion
          WHERE (actor1_tipo='empleado' AND actor1_id=?)
             OR (actor2_tipo='empleado' AND actor2_id=?)
        ");
        $st->bind_param("ii", $id, $id); $st->execute(); $st->close();
      }
      if (tableExists($conn, 'favoritos')) {
        $st = $conn->prepare("
          DELETE FROM favoritos
          WHERE (actor_tipo='empleado' AND actor_id=?)
             OR (objetivo_tipo='empleado' AND objetivo_id=?)
        ");
        $st->bind_param("ii", $id, $id); $st->execute(); $st->close();
      }
      if (tableExists($conn, 'notificaciones')) {
        $st = $conn->prepare("
          DELETE FROM notificaciones
          WHERE (actor_tipo='empleado' AND actor_id=?)
             OR (destino_tipo='empleado' AND destino_id=?)
        ");
        $st->bind_param("ii", $id, $id); $st->execute(); $st->close();
      }

      // 2) Borrar archivos del empleado
      $projectRoot = realpath(__DIR__ . '/..');                    // labora_db/
      $uploadsRoot = $projectRoot . DIRECTORY_SEPARATOR . 'uploads';

      if ($emp && $projectRoot && is_dir($projectRoot)) {
        $candidatos = [
          $emp['foto_perfil'] ?? null,
          $emp['dni_frente_path'] ?? null,
          $emp['dni_dorso_path'] ?? null,
          $emp['matricula_path'] ?? null,
        ];
        foreach ($candidatos as $rel) {
          if (!$rel) continue;
          if (preg_match('~^https?://~i', $rel) || str_starts_with($rel, '/')) continue;

          if (str_starts_with($rel, 'uploads/')) {
            $abs = realpath($projectRoot . DIRECTORY_SEPARATOR . $rel) ?: ($projectRoot . DIRECTORY_SEPARATOR . $rel);
          } else {
            $abs = realpath($uploadsRoot . DIRECTORY_SEPARATOR . $rel) ?: ($uploadsRoot . DIRECTORY_SEPARATOR . $rel);
          }
          if (str_starts_with($abs, $projectRoot) && is_file($abs)) { @unlink($abs); }
        }
      }

      // 3) Borrar empleado (educacion/experiencia tienen ON DELETE CASCADE)
      $del = $conn->prepare("DELETE FROM empleado WHERE id_empleado = ? LIMIT 1");
      $del->bind_param("i", $id);
      $del->execute();
      $del->close();

      $conn->commit();

    } catch (Throwable $e) {
      $conn->rollback();
      error_log('[ADMIN EMPLEADO DELETE ERROR] '.$e->getMessage());
      header("Location: {$BASE_URL}/vistas/admin/admin-panel.php?err=del_fail#trabajadores");
      exit();
    }

    // Email de rechazo (ya tengo $emp)
    if ($emp) { enviarDecisionTrab($emp['correo'] ?? '', $emp['nombre'] ?? '', 'rechazar', $obs); }

    header("Location: {$BASE_URL}/vistas/admin/admin-panel.php#trabajadores");
    exit();
  }

  // === aprobar / pendiente → UPDATE ===
  $nuevo = $accion === 'aprobar' ? 'aprobado' : 'pendiente';
  $sql = "
    UPDATE empleado
       SET estado_verificacion = ?,
           verificado_por      = ?,
           fecha_verificacion  = NOW(),
           observaciones_verificacion = ?,
           profesion           = COALESCE(NULLIF(?, ''), profesion),
           titulo_profesional  = COALESCE(NULLIF(?, ''), titulo_profesional)
     WHERE id_empleado = ?
  ";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sisssi", $nuevo, $admin_id, $obs, $profesion, $titulo_profesional, $id);
  $stmt->execute();
  $stmt->close();

  if ($emp) { enviarDecisionTrab($emp['correo'] ?? '', $emp['nombre'] ?? '', $accion, $obs); }
}

// Redirige siempre al final
header("Location: {$BASE_URL}/vistas/admin/admin-panel.php#trabajadores");
exit();
