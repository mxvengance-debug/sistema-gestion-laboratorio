<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mantenimientos</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Lista de Mantenimientos</h1>

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

    $sql = "SELECT * FROM Mantenimientos";
    $resultado = sqlsrv_query($conn,$sql);

    echo "<table border='1'>";

    echo "<tr>
            <th>ID</th>
            <th>ID Componente</th>
            <th>Fecha</th>
            <th>Descripción</th>
          </tr>";

    while($fila = sqlsrv_fetch_array($resultado, SQLSRV_FETCH_ASSOC)){

        echo "<tr>";

        echo "<td>".$fila['IdMantenimiento']."</td>";
        echo "<td>".$fila['IdComponente']."</td>";

        if($fila['Fecha'] != null){
            echo "<td>".$fila['Fecha']->format('Y-m-d')."</td>";
        }else{
            echo "<td>-</td>";
        }

        echo "<td>".$fila['Descripcion']."</td>";

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