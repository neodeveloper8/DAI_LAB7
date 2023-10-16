<?php
if (!isset($_GET['codigo'])) {
    header('Location: index.php?mensaje=error');
    exit();

}

include 'model/conexion.php';
$codigo = $_GET['codigo'];

$sentencia = $bd->prepare("SELECT pro.promocion, pro.duracion , pro.id_persona, per.nombres , per.apellido_paterno ,per.apellido_materno,per.celular , per.fecha_nacimiento 
  FROM promociones pro 
  INNER JOIN persona per ON per.id = pro.id_persona 
  WHERE pro.id = ?;");
$sentencia->execute([$codigo]);
$persona = $sentencia->fetch(PDO::FETCH_OBJ);

$url = 'https://api.green-api.com/waInstance7103864954/SendMessage/2a67ae0e8e534be3b1d036d52d1b7959ed911b894f6e40d68d';
$data = [
    "chatId" => "51".$persona->celular."@c.us",
    "message" =>  'Estimado(a) *'.strtoupper($persona->nombres).' '.strtoupper($persona->apellido_paterno).' '.strtoupper($persona->apellido_materno).'* No se pierda *'.strtoupper($persona->promocion).'* valido solo *'.$persona->duracion.'*'
];
$options = array(
    'http' => array(
        'method'  => 'POST',
        'content' => json_encode($data),
        'header' =>  "Content-Type: application/json\r\n" .
            "Accept: application/json\r\n"
    )
);

$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
$response = json_decode($result);
header('Location: agregarPromocion.php?codigo='.$persona->id_persona);
?>