<?php

header("Content-Type: application/json; charset=UTF-8");

require_once("conexion.php");

$sql = "SELECT
            M.IdMantenimiento,
            C.Nombre AS Componente,
            M.Fecha,
            M.Descripcion
        FROM Mantenimientos M
        INNER JOIN Componentes C
            ON M.IdComponente = C.IdComponente";

$stmt = sqlsrv_query($conn, $sql);

if ($stmt === false) {

    http_response_code(500);

    echo json_encode(array(
        "estado" => "error",
        "mensaje" => "Error al consultar los mantenimientos",
        "detalle" => sqlsrv_errors()
    ));

    exit();
}

$mantenimientos = array();

while($fila = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){

    $mantenimientos[] = array(

        "IdMantenimiento" => $fila["IdMantenimiento"],

        "Componente" => $fila["Componente"],

        "Fecha" => $fila["Fecha"]->format("Y-m-d"),

        "Descripcion" => $fila["Descripcion"]

    );

}

echo json_encode($mantenimientos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

?>