<?php
// /labora_db/funciones/user-email-change-confirm.php
require __DIR__ . '/../config/conexion.php';
mysqli_set_charset($conn, 'utf8mb4');

$token = $_GET['token'] ?? '';
if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
  http_response_code(400);
  exit('Token inválido.');
}
$confirmHash = hash('sha256', $token);

$sql = "SELECT id_usuario, email_change_new, email_confirm_expires 
        FROM usuarios 
        WHERE email_confirm_token_hash = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $confirmHash);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();

if (!$row) {
  http_response_code(400);
  exit('Enlace inválido.');
}
if (empty($row['email_change_new'])) {
  http_response_code(400);
  exit('No hay un correo pendiente para confirmar.');
}

$now = new DateTime();
$exp = new DateTime($row['email_confirm_expires']);
if ($exp <= $now) {
  $clr = $conn->prepare("UPDATE usuarios 
    SET email_confirm_token_hash=NULL, email_confirm_expires=NULL, email_change_new=NULL 
    WHERE id_usuario=?");
  $clr->bind_param("i", $row['id_usuario']);
  $clr->execute();
  http_response_code(400);
  exit('El enlace expiró. Iniciá el proceso de nuevo.');
}

// Aplicar cambio y limpiar temporales
$up = $conn->prepare("UPDATE usuarios SET 
    correo = email_change_new,
    email_change_new = NULL,
    email_change_token_hash = NULL,
    email_change_expires = NULL,
    email_confirm_token_hash = NULL,
    email_confirm_expires = NULL
  WHERE id_usuario = ?");
$up->bind_param("i", $row['id_usuario']);
if ($up->execute()) {
  header('Location: /labora_db/mensajes/correo-actualizado-user.html'); exit();
} else {
  http_response_code(500);
  exit('No se pudo confirmar el cambio. Probá de nuevo.');
}
