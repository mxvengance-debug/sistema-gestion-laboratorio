<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Préstamos</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Lista de Préstamos</h1>

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

    $sql = "SELECT * FROM Prestamos";
    $resultado = sqlsrv_query($conn,$sql);

    echo "<table border='1'>";
    echo "<tr>
            <th>ID</th>
            <th>ID Usuario</th>
            <th>Fecha Préstamo</th>
            <th>Fecha Límite</th>
            <th>Estado</th>
          </tr>";

    while($fila = sqlsrv_fetch_array($resultado, SQLSRV_FETCH_ASSOC)){

        echo "<tr>";

        echo "<td>".$fila['IdPrestamo']."</td>";
        echo "<td>".$fila['IdUsuario']."</td>";

        echo "<td>".$fila['FechaPrestamo']->format('Y-m-d')."</td>";
        echo "<td>".$fila['FechaLimite']->format('Y-m-d')."</td>";

        echo "<td>".$fila['Estado']."</td>";

        echo "</tr>";
    }

    echo "</table>";

}else{

    echo "Error de conexión";
}

?>

<br><br>

<a href="index.php">← Volver al inicio</a>

</body>
</html>