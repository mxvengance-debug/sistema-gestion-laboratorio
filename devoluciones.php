<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Devoluciones</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Lista de Devoluciones</h1>

<?php

$serverName = "localhost";

$connectionOptions = array(
    "Database" => "LaboratorioDB",
    "Uid" => "",
    "PWD" => "",
    "TrustServerCertificate" => true
);

$conn = sqlsrv_connect($serverName, $connectionOptions);

if($conn){

    $sql = "SELECT * FROM Devoluciones";
    $resultado = sqlsrv_query($conn,$sql);

    echo "<table border='1'>";
    echo "<tr>
            <th>ID Devolución</th>
            <th>ID Préstamo</th>
            <th>Fecha de Devolución</th>
            <th>Observaciones</th>
          </tr>";

    while($fila = sqlsrv_fetch_array($resultado, SQLSRV_FETCH_ASSOC)){

        echo "<tr>";

        echo "<td>".$fila['IdDevolucion']."</td>";
        echo "<td>".$fila['IdPrestamo']."</td>";

        if($fila['FechaDevolucion'] != null){
            echo "<td>".$fila['FechaDevolucion']->format('Y-m-d')."</td>";
        }else{
            echo "<td>-</td>";
        }

        echo "<td>".$fila['Observaciones']."</td>";

        echo "</tr>";
    }

    echo "</table>";

}else{

    echo "<h2>Error al conectar con la base de datos.</h2>";

}

?>

<br><br>

<a href="index.php">⬅ Volver al inicio</a>

</body>
</html>