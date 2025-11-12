<?php
// labora_db/funciones/admin-panel-editar-usuario.php
session_start();
if (empty($_SESSION['admin'])) {
  header("Location: /labora_db/vistas/admin/admin-login.php");
  exit();
}

require_once __DIR__ . '/../config/conexion.php';
mysqli_set_charset($conn, 'utf8mb4');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = (int)($_POST['id_usuario'] ?? 0);
  if ($id <= 0) { header("Location: /labora_db/vistas/admin/admin-panel.php#usuarios"); exit(); }

  $nombre   = trim($_POST['nombre'] ?? '');
  $correo   = trim($_POST['correo'] ?? '');
  $telefono = trim($_POST['telefono'] ?? '');
  $dni      = trim($_POST['dni'] ?? '');
  $fnac     = trim($_POST['fecha_nacimiento'] ?? '');
  $localidad= trim($_POST['localidad'] ?? '');
  $direccion= trim($_POST['direccion'] ?? '');

  $errores = [];
  if ($nombre === '')  $errores[] = 'El nombre es obligatorio';
  if ($correo === '')  $errores[] = 'El correo es obligatorio';
  if ($fnac !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fnac)) $errores[] = 'Fecha de nacimiento debe ser YYYY-MM-DD';

  if (!empty($errores)) {
    $q = http_build_query(['id' => $id, 'err' => implode(' | ', $errores)]);
    header("Location: /labora_db/funciones/admin-panel-editar-usuario.php?$q");
    exit();
  }

  $sql = "UPDATE usuarios
             SET nombre = ?, correo = ?, telefono = ?, dni = ?, fecha_nacimiento = ?, localidad = ?, direccion = ?
           WHERE id_usuario = ?";
  $st = $conn->prepare($sql);
  $st->bind_param("sssssssi", $nombre, $correo, $telefono, $dni, $fnac, $localidad, $direccion, $id);

  if (!$st->execute()) {
    $q = http_build_query(['id' => $id, 'err' => 'No se pudo guardar: ' . $st->error]);
    header("Location: /labora_db/funciones/admin-panel-editar-usuario.php?$q");
    exit();
  }
  header("Location: /labora_db/vistas/admin/admin-panel.php#usuarios");
  exit();
}

if ($id <= 0) { header("Location: /labora_db/vistas/admin/admin-panel.php#usuarios"); exit(); }

$st = $conn->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
$st->bind_param("i", $id);
$st->execute();
$res = $st->get_result();
if (!$res || $res->num_rows !== 1) {
  header("Location: /labora_db/vistas/admin/admin-panel.php#usuarios");
  exit();
}
$u = $res->fetch_assoc();
$errMsg = $_GET['err'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Editar usuario #<?= (int)$u['id_usuario'] ?> - LABORA</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
  :root{--bg:#f4f6f8; --acc:#2563eb;}
  *{box-sizing:border-box} body{margin:0; font-family:system-ui,Segoe UI,Roboto,Arial; background:var(--bg)}
  .wrap{max-width:900px; margin:30px auto; background:#fff; border-radius:14px; padding:20px; box-shadow:0 6px 18px rgba(0,0,0,.06)}
  h2{margin:0 0 14px}
  .grid{display:grid; grid-template-columns: 1fr 1fr; gap:12px}
  label{font-weight:600; font-size:14px; color:#334155}
  input, textarea{width:100%; padding:10px; border:1px solid #d1d5db; border-radius:10px}
  textarea{min-height:100px}
  .row{display:flex; gap:10px; flex-wrap:wrap; margin-top:14px}
  .btn{padding:10px 12px; border:0; border-radius:10px; cursor:pointer; font-weight:700}
  .btn.save{background:#16a34a; color:white}
  .btn.back{background:#e5e7eb}
  .error{background:#fee2e2; color:#7f1d1d; padding:10px 12px; border-radius:10px; margin-bottom:12px}
  @media (max-width: 860px){ .grid{grid-template-columns:1fr} }
</style>
</head>
<body>
<div class="wrap">
  <h2>Editar usuario #<?= (int)$u['id_usuario'] ?></h2>
  <?php if ($errMsg): ?><div class="error"><?= htmlspecialchars($errMsg) ?></div><?php endif; ?>

  <form method="post" action="/labora_db/funciones/admin-panel-editar-usuario.php">
    <input type="hidden" name="id_usuario" value="<?= (int)$u['id_usuario'] ?>">

    <div class="grid">
      <div>
        <label>Nombre</label>
        <input name="nombre" value="<?= htmlspecialchars($u['nombre'] ?? '') ?>" required>
      </div>
      <div>
        <label>Correo</label>
        <input name="correo" type="email" value="<?= htmlspecialchars($u['correo'] ?? '') ?>" required>
      </div>

      <div>
        <label>Teléfono</label>
        <input name="telefono" value="<?= htmlspecialchars($u['telefono'] ?? '') ?>">
      </div>
      <div>
        <label>DNI</label>
        <input name="dni" value="<?= htmlspecialchars($u['dni'] ?? '') ?>">
      </div>

      <div>
        <label>Fecha de nacimiento (YYYY-MM-DD)</label>
        <input name="fecha_nacimiento" value="<?= htmlspecialchars($u['fecha_nacimiento'] ?? '') ?>">
      </div>
      <div>
        <label>Localidad</label>
        <input name="localidad" value="<?= htmlspecialchars($u['localidad'] ?? '') ?>">
      </div>
    </div>

    <div style="margin-top:12px">
      <label>Dirección</label>
      <input name="direccion" value="<?= htmlspecialchars($u['direccion'] ?? '') ?>">
    </div>

    <div class="row">
      <button class="btn save" type="submit">Guardar cambios</button>
      <a class="btn back" href="/labora_db/vistas/admin/admin-panel.php#usuarios">Volver</a>
      <a class="btn back" href="/labora_db/vistas/admin/admin-usuario.php?id=<?= (int)$u['id_usuario'] ?>">Ver ficha</a>
    </div>
  </form>
</div>
</body>
</html>
