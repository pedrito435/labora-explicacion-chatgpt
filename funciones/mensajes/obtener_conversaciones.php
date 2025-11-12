<?php
// labora_db/funciones/mensajes/obtener_conversaciones.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json; charset=utf-8');
session_start();

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/utils.php';
mysqli_set_charset($conn,'utf8mb4');

$yo = ms_current_actor();
if (!$yo) { echo json_encode([]); exit; }

$tipoYo = $yo['tipo'];
$idYo   = (int)$yo['id'];

$sql = "
SELECT sub.otro_tipo AS tipo, sub.otro_id AS id,
       MAX(sub.fecha_envio) AS ultima_fecha,
       SUM(CASE WHEN sub.receptor_tipo = ? AND sub.receptor_id = ? AND sub.leido = 0 THEN 1 ELSE 0 END) AS no_leidos
FROM (
  SELECT emisor_tipo AS yo_tipo, emisor_id AS yo_id, receptor_tipo AS otro_tipo, receptor_id AS otro_id,
         mensaje, fecha_envio, leido, receptor_tipo, receptor_id
    FROM mensajes
   WHERE emisor_tipo = ? AND emisor_id = ?
  UNION ALL
  SELECT receptor_tipo AS yo_tipo, receptor_id AS yo_id, emisor_tipo AS otro_tipo, emisor_id AS otro_id,
         mensaje, fecha_envio, leido, receptor_tipo, receptor_id
    FROM mensajes
   WHERE receptor_tipo = ? AND receptor_id = ?
) AS sub
GROUP BY sub.otro_tipo, sub.otro_id
ORDER BY ultima_fecha DESC
LIMIT 100";

$stmt = $conn->prepare($sql);
$stmt->bind_param('sisisi', $tipoYo,$idYo,$tipoYo,$idYo,$tipoYo,$idYo);
$stmt->execute();
$res  = $stmt->get_result();
$rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
$stmt->close();

$out = [];
foreach ($rows as $row) {
  $p = ms_persona($conn, $row['tipo'], (int)$row['id']);

  $s2 = $conn->prepare("
    SELECT mensaje, fecha_envio
      FROM mensajes
     WHERE (emisor_tipo=? AND emisor_id=? AND receptor_tipo=? AND receptor_id=?)
        OR (emisor_tipo=? AND emisor_id=? AND receptor_tipo=? AND receptor_id=?)
     ORDER BY fecha_envio DESC, id_mensaje DESC
     LIMIT 1
  ");
  $s2->bind_param('sisisisi', $tipoYo,$idYo,$row['tipo'],$row['id'], $row['tipo'],$row['id'],$tipoYo,$idYo);
  $s2->execute();
  $r2   = $s2->get_result();
  $last = $r2 ? $r2->fetch_assoc() : null;
  $s2->close();

  $hora = $last ? date('H:i', strtotime($last['fecha_envio'])) : '';

  $out[] = [
    'tipo'           => $row['tipo'],
    'id'             => (int)$row['id'],
    'nombre'         => $p['nombre'],
    'iniciales'      => $p['iniciales'],
    'ultimo_mensaje' => $last['mensaje'] ?? '',
    'hora'           => $hora,
    'no_leidos'      => (int)($row['no_leidos'] ?? 0),
  ];
}

echo json_encode($out, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
