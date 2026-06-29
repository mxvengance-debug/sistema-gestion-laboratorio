<?php

$serverName = "localhost";

$connectionOptions = array(
    "Database" => "LaboratorioDB",
    "Uid" => "",
    "PWD" => "",
    "CharacterSet" => "UTF-8"
);

$conn = sqlsrv_connect($serverName, $connectionOptions);

$sql = "SELECT * FROM Componentes";

$resultado = sqlsrv_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <title>Componentes</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<div class="contenedor">

    <h1>Lista de Componentes</h1>

    <table border="1" cellpadding="10">

        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Categoría</th>
            <th>Cantidad</th>
            <th>Estado</th>
        </tr>

        <?php

        while($fila = sqlsrv_fetch_array($resultado, SQLSRV_FETCH_ASSOC)){

            echo "<tr>";

            echo "<td>".$fila['IdComponente']."</td>";
            echo "<td>".$fila['Nombre']."</td>";
            echo "<td>".$fila['Categoria']."</td>";
            echo "<td>".$fila['CantidadDisponible']."</td>";
            echo "<td>".$fila['Estado']."</td>";

            echo "</tr>";
        }

        ?>

    </table>

    <br><br>

    <a href="index.php">⬅ Volver al inicio</a>

</div>

</body>

</html>