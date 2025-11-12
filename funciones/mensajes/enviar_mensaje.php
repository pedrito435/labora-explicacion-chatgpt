<?php
// labora_db/funciones/mensajes/enviar_mensaje.php
ini_set('display_errors',0);
header('Content-Type: application/json; charset=utf-8');
session_start();

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/utils.php';
mysqli_set_charset($conn,'utf8mb4');

$yo = ms_current_actor();
if (!$yo) { echo json_encode(['ok'=>false,'msg'=>'no auth']); exit; }

$payload = json_decode(file_get_contents('php://input'), true);
$receptor_tipo = ($payload['receptor_tipo'] ?? '') === 'empleado' ? 'empleado' : 'usuario';
$receptor_id   = (int)($payload['receptor_id'] ?? 0);
$mensaje       = trim((string)($payload['mensaje'] ?? ''));

if ($receptor_id<=0 || $mensaje==='') { echo json_encode(['ok'=>false,'msg'=>'parámetros inválidos']); exit; }

/* 🔒 no te mandes mensaje a vos mismo */
if ($yo['tipo']===$receptor_tipo && (int)$yo['id']===$receptor_id) {
  echo json_encode(['ok'=>false,'msg'=>'no podés enviarte mensajes a vos mismo']); exit;
}

/* validar que receptor exista */
if($receptor_tipo==='empleado'){
  $q=$conn->prepare("SELECT 1 FROM empleado WHERE id_empleado=?");
} else {
  $q=$conn->prepare("SELECT 1 FROM usuarios WHERE id_usuario=?");
}
$q->bind_param('i',$receptor_id); 
$q->execute(); 
$r=$q->get_result();
$q->close();

if(!$r || $r->num_rows==0){ echo json_encode(['ok'=>false,'msg'=>'receptor inexistente']); exit; }

/* Insertar */
$sql="INSERT INTO mensajes (emisor_tipo,emisor_id,receptor_tipo,receptor_id,mensaje,fecha_envio,leido)
      VALUES (?,?,?,?,?,NOW(),0)";
$stmt=$conn->prepare($sql);
$stmt->bind_param('sisis', $yo['tipo'],$yo['id'],$receptor_tipo,$receptor_id,$mensaje);
$ok=$stmt->execute();
$stmt->close();

echo json_encode(['ok'=>$ok?true:false]);
