<?php

header("Content-Type: application/json; charset=UTF-8");

$serverName = "localhost";

$connectionOptions = array(
    "Database" => "LaboratorioDB",
    "UID" => "",
    "PWD" => "",
    "CharacterSet" => "UTF-8"
);

$conn = sqlsrv_connect($serverName, $connectionOptions);

if ($conn === false) {

    http_response_code(500);

    echo json_encode(array(
        "estado" => "error",
        "mensaje" => "No se pudo conectar con SQL Server",
        "detalle" => sqlsrv_errors()
    ));

    exit();
}