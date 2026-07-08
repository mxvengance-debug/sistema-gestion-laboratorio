<?php

header("Content-Type: application/json; charset=UTF-8");

require_once("conexion.php");

$sql = "SELECT
            P.IdPrestamo,
            U.Nombre AS Usuario,
            P.FechaPrestamo,
            P.FechaLimite,
            P.Estado
        FROM Prestamos P
        INNER JOIN Usuarios U
            ON P.IdUsuario = U.IdUsuario";

$stmt = sqlsrv_query($conn, $sql);

if ($stmt === false) {

    http_response_code(500);

    echo json_encode(array(
        "estado" => "error",
        "mensaje" => "Error al consultar los préstamos",
        "detalle" => sqlsrv_errors()
    ));

    exit();
}

$prestamos = array();

while($fila = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){

    $prestamos[] = array(

        "IdPrestamo" => $fila["IdPrestamo"],

        "Usuario" => $fila["Usuario"],

        "FechaPrestamo" => $fila["FechaPrestamo"]->format("Y-m-d"),

        "FechaLimite" => $fila["FechaLimite"]->format("Y-m-d"),

        "Estado" => $fila["Estado"]

    );

}

echo json_encode($prestamos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

?>