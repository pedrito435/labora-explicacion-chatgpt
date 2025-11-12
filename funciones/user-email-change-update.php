<?php
// /labora_db/funciones/user-email-change-update.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../PHPMailer-master/src/Exception.php';
require __DIR__ . '/../PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/../PHPMailer-master/src/SMTP.php';
require __DIR__ . '/../config/conexion.php';
mysqli_set_charset($conn, 'utf8mb4');

$token = $_POST['token'] ?? '';
$mail1 = trim($_POST['mail1'] ?? '');
$mail2 = trim($_POST['mail2'] ?? '');

if (!preg_match('/^[a-f0-9]{64}$/', $token)) { http_response_code(400); exit('Token inválido.'); }
if ($mail1 === '' || $mail2 === '')          { http_response_code(400); exit('Los correos no pueden estar vacíos.'); }
if ($mail1 !== $mail2)                        { http_response_code(400); exit('Los correos no coinciden.'); }
if (!filter_var($mail1, FILTER_VALIDATE_EMAIL)){ http_response_code(400); exit('Correo inválido.'); }

$tokenHash = hash('sha256', $token);
$q = $conn->prepare("SELECT id_usuario, email_change_expires FROM usuarios WHERE email_change_token_hash=? LIMIT 1");
$q->bind_param("s", $tokenHash);
$q->execute();
$u = $q->get_result()->fetch_assoc();
$q->close();

if (!$u) { http_response_code(400); exit('Enlace inválido.'); }
$now = new DateTime();
$exp = new DateTime($u['email_change_expires']);
if ($exp <= $now) {
  $clr = $conn->prepare("UPDATE usuarios SET email_change_token_hash=NULL, email_change_expires=NULL WHERE id_usuario=?");
  $clr->bind_param("i", $u['id_usuario']); $clr->execute();
  http_response_code(400); exit('El enlace expiró. Volvé a solicitarlo.');
}

// Unicidad
$chk = $conn->prepare("SELECT 1 FROM usuarios WHERE correo=? AND id_usuario<>? LIMIT 1");
$chk->bind_param("si", $mail1, $u['id_usuario']);
$chk->execute();
if ($chk->get_result()->num_rows > 0) { http_response_code(409); exit('Ese correo ya está en uso.'); }

// Token de confirmación al nuevo correo
$rawConfirm  = bin2hex(random_bytes(32));
$confirmHash = hash('sha256', $rawConfirm);
$confirmExp  = (new DateTime('+60 minutes'))->format('Y-m-d H:i:s');

$up = $conn->prepare("UPDATE usuarios 
  SET email_change_new=?, email_confirm_token_hash=?, email_confirm_expires=?
  WHERE id_usuario=?");
$up->bind_param("sssi", $mail1, $confirmHash, $confirmExp, $u['id_usuario']);
if (!$up->execute()) { http_response_code(500); exit('No se pudo preparar la confirmación.'); }
$up->close();

$confirmLink = "http://localhost/labora_db/funciones/user-email-change-confirm.php?token=" . urlencode($rawConfirm);

$html = "
<!DOCTYPE html><html lang='es'><head><meta charset='UTF-8'></head>
<body style='margin:0; padding:0; font-family: Arial, sans-serif; background:#f4f4f4;'>
  <table align='center' width='100%' cellpadding='0' cellspacing='0' style='padding:20px 0;'>
    <tr><td align='center'>
      <table width='600' cellpadding='0' cellspacing='0' style='background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 0 10px rgba(0,0,0,.1)'>
        <tr><td style='padding:40px 30px'>
          <h2 style='color:#0a5b84;margin:0 0 10px;'>Cambiar cuenta – Labora</h2>
          <p style='font-size:16px;color:#555'>Están intentando asociar este correo (<b>".htmlspecialchars($mail1,ENT_QUOTES)."</b>) a una cuenta de <b>Labora</b>.</p>
          <p style='font-size:16px;color:#555'>Si vos solicitaste esto, confirmalo haciendo clic en el botón (vigente 60 minutos):</p>
          <div style='text-align:center;margin:30px 0;'>
            <a href='".htmlspecialchars($confirmLink,ENT_QUOTES)."' style='background:#00B4D8;color:#fff;padding:14px 22px;border-radius:6px;text-decoration:none;font-weight:600'>Confirmar asociación de correo</a>
          </div>
          <p style='font-size:14px;color:#999'>Si no fuiste vos, ignorá este correo y el cambio no se aplicará.</p>
        </td></tr>
      </table>
    </td></tr>
  </table>
</body></html>
";

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
  $mail->addAddress($mail1);
  $mail->isHTML(true);
  $mail->Subject = 'Cambiar cuenta - Labora';
  $mail->Body    = $html;
  $mail->AltBody = "Están intentando asociar este correo a una cuenta de Labora. Confirmá aquí (60 min): $confirmLink";

  $mail->send();
} catch (Exception $e) {
  if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST']==='localhost') {
    echo "<p><strong>[DEV]</strong> No se pudo enviar el mail al nuevo correo, pero podés confirmar desde este enlace:</p>";
    echo "<p><a href='".htmlspecialchars($confirmLink,ENT_QUOTES)."'>".$confirmLink."</a></p>";
    exit;
  } else {
    http_response_code(500);
    exit('No pudimos enviar el correo de confirmación al nuevo email.');
  }
}

header('Location: /labora_db/mensajes/revisar-nuevo-mail.html'); exit;
