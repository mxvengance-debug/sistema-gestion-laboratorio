<?php

header("Content-Type: application/json; charset=UTF-8");

require_once("conexion.php");

$sql = "SELECT IdComponente, Nombre, Categoria, CantidadDisponible, Estado FROM Componentes";

$stmt = sqlsrv_query($conn, $sql);

if ($stmt === false) {

    http_response_code(500);

    echo json_encode(array(
        "estado" => "error",
        "mensaje" => "Error al consultar la tabla Componentes",
        "detalle" => sqlsrv_errors()
    ));

    exit();
}

$componentes = array();

while ($fila = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {

    $componentes[] = array(
        "IdComponente" => $fila["IdComponente"],
        "Nombre" => $fila["Nombre"],
        "Categoria" => $fila["Categoria"],
        "CantidadDisponible" => $fila["CantidadDisponible"],
        "Estado" => $fila["Estado"]
    );

}

echo json_encode($componentes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

?>