<?php
// labora_db/funciones/mensajes/obtener_mensajes.php
ini_set('display_errors',0);
header('Content-Type: application/json; charset=utf-8');
session_start();

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/utils.php';
mysqli_set_charset($conn,'utf8mb4');

$yo = ms_current_actor();
if (!$yo) { echo json_encode([]); exit; }

$tipoYo = $yo['tipo'];
$idYo   = (int)$yo['id'];

$tipoOtro = isset($_GET['tipo_otro']) ? ($_GET['tipo_otro']==='empleado'?'empleado':'usuario') : null;
$idOtro   = isset($_GET['id_otro']) ? (int)$_GET['id_otro'] : 0;
if(!$tipoOtro || $idOtro<=0){ echo json_encode([]); exit; }

/* 🔒 Seguro: confirmamos que existe relación (vos ↔ otro) */
$chk = $conn->prepare("
  SELECT 1
    FROM mensajes
   WHERE (emisor_tipo=? AND emisor_id=? AND receptor_tipo=? AND receptor_id=?)
      OR (emisor_tipo=? AND emisor_id=? AND receptor_tipo=? AND receptor_id=?)
   LIMIT 1
");
$chk->bind_param('sisisisi', $tipoYo,$idYo,$tipoOtro,$idOtro, $tipoOtro,$idOtro,$tipoYo,$idYo);
$chk->execute();
$has = $chk->get_result();
$chk->close();

if(!$has || $has->num_rows===0){
  // si todavía no hay mensajes entre ustedes, devolvemos vacío (nuevo chat)
  echo json_encode([]); 
  exit;
}

$sql = "SELECT id_mensaje, emisor_tipo, emisor_id, receptor_tipo, receptor_id, mensaje, fecha_envio
          FROM mensajes
         WHERE (emisor_tipo=? AND emisor_id=? AND receptor_tipo=? AND receptor_id=?)
            OR (emisor_tipo=? AND emisor_id=? AND receptor_tipo=? AND receptor_id=?)
      ORDER BY fecha_envio ASC, id_mensaje ASC
         LIMIT 500";
$stmt=$conn->prepare($sql);
$stmt->bind_param('sisisisi', $tipoYo,$idYo,$tipoOtro,$idOtro, $tipoOtro,$idOtro,$tipoYo,$idYo);
$stmt->execute();
$res=$stmt->get_result();

$out=[];
while($m=$res->fetch_assoc()){
  $out[] = [
    'id_mensaje'  => (int)$m['id_mensaje'],
    'emisor_tipo' => $m['emisor_tipo'],
    'emisor_id'   => (int)$m['emisor_id'],
    'mensaje'     => $m['mensaje'],
    'fecha'       => $m['fecha_envio'],
    'hora'        => date('H:i', strtotime($m['fecha_envio'])),
  ];
}
$stmt->close();

/* Marcar como leídos (entrantes hacia mí) */
$upd=$conn->prepare("UPDATE mensajes SET leido=1
                      WHERE receptor_tipo=? AND receptor_id=? AND emisor_tipo=? AND emisor_id=? AND leido=0");
$upd->bind_param('sisi', $tipoYo,$idYo,$tipoOtro,$idOtro);
$upd->execute(); 
$upd->close();

echo json_encode($out, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
