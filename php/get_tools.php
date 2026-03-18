<?php
header('Content-Type: application/json');

$conexion = new mysqli("localhost", "root", "", "smartmachings");

if ($conexion->connect_error) {
    echo json_encode(["error" => "Error de conexión"]);
    exit;
}

$resultado = $conexion->query("SELECT id, nombre, categoria, descripcion, model_path, color, destacado, created_at FROM herramientas");

$herramientas = [];

while ($fila = $resultado->fetch_assoc()) {
    $herramientas[] = $fila;
}

$conexion->close();

echo json_encode($herramientas);
exit;