<?php
// /labora_db/funciones/user-email-change-request.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

require __DIR__ . '/../PHPMailer-master/src/Exception.php';
require __DIR__ . '/../PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/../PHPMailer-master/src/SMTP.php';
require __DIR__ . '/../config/conexion.php';
mysqli_set_charset($conn, 'utf8mb4');

if (empty($_SESSION['usuario_id'])) {
  header('Location: /labora_db/vistas/formularios/login-options.html'); exit;
}
$idU = (int)$_SESSION['usuario_id'];

// Generar token y guardar (hash + expiración) en usuarios
$rawToken  = bin2hex(random_bytes(32)); // 64 hex
$tokenHash = hash('sha256', $rawToken);
$expiresAt = (new DateTime('+60 minutes'))->format('Y-m-d H:i:s');

$up = $conn->prepare("UPDATE usuarios 
  SET email_change_token_hash=?, email_change_expires=? 
  WHERE id_usuario=?");
$up->bind_param("ssi", $tokenHash, $expiresAt, $idU);
if (!$up->execute()) {
  $_SESSION['flash_err'] = "No se pudo iniciar el cambio de correo.";
  header('Location: /labora_db/vistas/usuarios/configuracion.php'); exit;
}
$up->close();

// Traer correo actual y nombre
$q = $conn->prepare("SELECT correo, nombre FROM usuarios WHERE id_usuario=? LIMIT 1");
$q->bind_param("i", $idU);
$q->execute();
$row = $q->get_result()->fetch_assoc();
$q->close();

if (!$row || empty($row['correo'])) {
  $_SESSION['flash_err'] = "No se pudo enviar el enlace (sin correo actual).";
  header('Location: /labora_db/vistas/usuarios/configuracion.php'); exit;
}

$verifyLink = "http://localhost/labora_db/funciones/user-email-change-verify.php?token=" . urlencode($rawToken);
$nombre = $row['nombre'] ?: 'Usuario';
$destino = $row['correo'];

// Enviar correo al correo actual
$mail = new PHPMailer(true);
try {
  $mail->isSMTP();
  $mail->Host = 'smtp.gmail.com';
  $mail->SMTPAuth = true;
  $mail->Username = 'labora1357@gmail.com';
  $mail->Password = 'efrx dujz cwyw jtsj';
  $mail->SMTPSecure = 'tls';
  $mail->Port = 587;

  $mail->setFrom('labora1357@gmail.com', 'Labora');
  $mail->addAddress($destino);
  $mail->isHTML(true);
  $mail->Subject = 'Verifica cambio de correo - Labora';
  $mail->Body = "
  <!DOCTYPE html><html lang='es'><head><meta charset='UTF-8'></head>
  <body style='font-family:Arial,sans-serif;background:#f4f4f4;margin:0'>
    <table align='center' width='100%' cellpadding='0' cellspacing='0' style='padding:20px 0;'>
      <tr><td align='center'>
        <table width='600' cellpadding='0' cellspacing='0' style='background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 0 10px rgba(0,0,0,.1)'>
          <tr><td style='padding:40px 30px'>
            <h2 style='margin:0 0 10px;color:#333'>Verificá tu cambio de correo</h2>
            <p style='font-size:16px;color:#555'>Hola <b>".htmlspecialchars($nombre,ENT_QUOTES)."</b>, abrí este enlace para continuar con el cambio (vigencia 60 minutos):</p>
            <div style='text-align:center;margin:30px 0'>
              <a href='".htmlspecialchars($verifyLink,ENT_QUOTES)."' style='background:#00B4D8;color:#fff;padding:14px 22px;border-radius:6px;text-decoration:none;font-weight:600'>Continuar</a>
            </div>
          </td></tr>
        </table>
      </td></tr>
    </table>
  </body></html>";
  $mail->AltBody = "Continuá el cambio de correo (60 min): $verifyLink";

  $mail->send();
  $_SESSION['flash_ok'] = "Te enviamos un enlace a tu correo actual para continuar.";
} catch (Exception $e) {
  if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST']==='localhost') {
    $_SESSION['flash_ok'] = "[DEV] No se pudo enviar el mail. Link: <a href='".htmlspecialchars($verifyLink,ENT_QUOTES)."'>".$verifyLink."</a>";
  } else {
    $_SESSION['flash_err'] = "No pudimos enviar el correo. Probá más tarde.";
  }
}

header('Location: /labora_db/vistas/usuarios/configuracion.php'); exit;
