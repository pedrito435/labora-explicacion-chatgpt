<?php
// /labora_db/funciones/user-email-change-verify.php
require __DIR__ . '/../config/conexion.php';
mysqli_set_charset($conn, 'utf8mb4');

$token = $_GET['token'] ?? '';
if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
  http_response_code(400);
  exit('Token inválido.');
}
$tokenHash = hash('sha256', $token);

$sql = "SELECT id_usuario, email_change_expires FROM usuarios WHERE email_change_token_hash = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $tokenHash);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();

$valid = false;
if ($row) {
  $now = new DateTime();
  $exp = new DateTime($row['email_change_expires']);
  if ($exp > $now) $valid = true;
}
if (!$valid) {
  http_response_code(400);
  exit('El enlace es inválido o expiró. Volvé a solicitarlo.');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Cambiar correo - Usuario</title>
  <link rel="stylesheet" href="/labora_db/recursos/css/styles.css">
</head>
<body>
  <div class="form-section">
    <h2>Ingresá tu nuevo correo</h2>
    <form action="/labora_db/funciones/user-email-change-update.php" method="POST" id="mail-form">
      <input type="hidden" name="token" value="<?php echo htmlspecialchars($token,ENT_QUOTES); ?>">
      <div class="input-group">
        <label for="mail1">Nuevo correo</label>
        <input type="email" id="mail1" name="mail1" required>
      </div>
      <div class="input-group">
        <label for="mail2">Repetir nuevo correo</label>
        <input type="email" id="mail2" name="mail2" required>
      </div>
      <button type="submit">Continuar</button>
    </form>
  </div>
  <script>
    const f = document.getElementById('mail-form');
    f.addEventListener('submit', (e)=>{
      const m1 = document.getElementById('mail1').value.trim();
      const m2 = document.getElementById('mail2').value.trim();
      if (m1 === '' || m2 === '' || m1 !== m2) { e.preventDefault(); alert('Los correos no coinciden.'); }
    });
  </script>
</body>
</html>
