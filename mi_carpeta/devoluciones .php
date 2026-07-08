<?php

header("Content-Type: application/json; charset=UTF-8");

require_once("conexion.php");

$sql = "SELECT
            D.IdDevolucion,
            U.Nombre AS Usuario,
            D.FechaDevolucion,
            D.Observaciones
        FROM Devoluciones D
        INNER JOIN Prestamos P
            ON D.IdPrestamo = P.IdPrestamo
        INNER JOIN Usuarios U
            ON P.IdUsuario = U.IdUsuario";

$stmt = sqlsrv_query($conn, $sql);

if ($stmt === false) {

    http_response_code(500);

    echo json_encode(array(
        "estado" => "error",
        "mensaje" => "Error al consultar las devoluciones",
        "detalle" => sqlsrv_errors()
    ));

    exit();
}

$devoluciones = array();

while($fila = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){

    $devoluciones[] = array(

        "IdDevolucion" => $fila["IdDevolucion"],

        "Usuario" => $fila["Usuario"],

        "FechaDevolucion" => $fila["FechaDevolucion"]->format("Y-m-d"),

        "Observaciones" => $fila["Observaciones"]

    );

}

echo json_encode($devoluciones, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

?>