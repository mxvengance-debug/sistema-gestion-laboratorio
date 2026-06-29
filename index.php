<?php

$serverName = "localhost";

$connectionOptions = array(
    "Database" => "LaboratorioDB",
    "Uid" => "",
    "PWD" => "",
    "CharacterSet" => "UTF-8"
);

$conn = sqlsrv_connect($serverName, $connectionOptions);

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <title>Sistema de Laboratorio</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

    <div class="contenedor">

        <h1>Sistema Web de Gestión y Préstamo</h1>

        <?php
        if($conn){
            echo "<p class='correcto'> :) Conexión exitosa con SQL Server</p>";
        }else{
            echo "<p class='error'> :( Error de conexión</p>";
        }
        ?>

        <div class="menu">

            <a href="usuarios.php">Usuarios</a>
            <a href="componentes.php">Componentes</a>
            <a href="prestamos.php">Préstamos</a>
            <a href="devoluciones.php">Devoluciones</a>
            <a href="mantenimientos.php">Mantenimientos</a>

        </div>

    </div>

</body>

</html>