<?php

header("Content-Type: application/json; charset=UTF-8");

require_once("conexion.php");

$sql = "SELECT * FROM Usuarios";

$stmt = sqlsrv_query($conn, $sql);

if ($stmt === false) {

    http_response_code(500);

    echo json_encode(array(
        "estado" => "error",
        "mensaje" => "Error al consultar la tabla Usuarios",
        "detalle" => sqlsrv_errors()
    ));

    exit();
}

$usuarios = array();

while ($fila = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {

    $usuarios[] = array(
        "IdUsuario" => $fila["IdUsuario"],
        "Nombre" => $fila["Nombre"],
        "Correo" => $fila["Correo"],
        "Rol" => $fila["Rol"]
    );

}

echo json_encode($usuarios, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

?>