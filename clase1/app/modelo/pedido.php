<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Headers: *");

// Conectar a la base de datos
$conexion = new mysqli("localhost", "root", "", "empleado");

if ($conexion->connect_error) {
    die(json_encode(["mensaje" => "Conexión fallida: " . $conexion->connect_error]));
}

// Obtener datos del cuerpo del POST
$datos = json_decode(file_get_contents("php://input"), true);

if (!$datos) {
    echo json_encode(["mensaje" => "No se recibieron datos"]);
    exit;
}

// Validar datos requeridos
$camposRequeridos = ['producto', 'color', 'talla', 'cantidad', 'usuario', 'total'];
foreach ($camposRequeridos as $campo) {
    if (!isset($datos[$campo])) {
        echo json_encode(["mensaje" => "Falta el campo requerido: " . $campo]);
        exit;
    }
}

// Asignar datos recibidos
$producto = $datos["producto"];
$color = $datos["color"];
$talla = $datos["talla"];
$cantidad = $datos["cantidad"];
$nombre_cliente = $datos["usuario"]["nombre"];
$direccion = $datos["usuario"]["direccion"];
$telefono = $datos["usuario"]["telefono"];
$email = $datos["usuario"]["email"];
$total = $datos["total"];
$estado = "pendiente";
$fecha = date('Y-m-d H:i:s');

// Insertar en la base de datos
$sql = "INSERT INTO pedidos (
    producto, color, talla, cantidad, nombre_cliente, direccion, telefono, email, total, estado, fecha
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conexion->prepare($sql);
$stmt->bind_param(
    "sssisssssss", 
    $producto, $color, $talla, $cantidad, $nombre_cliente, 
    $direccion, $telefono, $email, $total, $estado, $fecha
);

if ($stmt->execute()) {
    echo json_encode(["mensaje" => "Pedido guardado correctamente"]);
} else {
    echo json_encode(["mensaje" => "Error al guardar el pedido: " . $stmt->error]);
}

$stmt->close();
$conexion->close();
?>