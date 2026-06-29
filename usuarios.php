<?php

$serverName = "localhost";

$connectionOptions = array(
    "Database" => "LaboratorioDB",
    "Uid" => "",
    "PWD" => "",
    "CharacterSet" => "UTF-8"
);

$conn = sqlsrv_connect($serverName, $connectionOptions);

$sql = "SELECT * FROM Usuarios";

$resultado = sqlsrv_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuarios</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="contenedor">

    <h1>Lista de Usuarios</h1>

    <table border="1" cellpadding="10">

        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Correo</th>
            <th>Password</th>
            <th>Rol</th>
        </tr>

        <?php

        while($fila = sqlsrv_fetch_array($resultado, SQLSRV_FETCH_ASSOC)){

            echo "<tr>";

            echo "<td>".$fila['IdUsuario']."</td>";
            echo "<td>".$fila['Nombre']."</td>";
            echo "<td>".$fila['Correo']."</td>";
            echo "<td>".$fila['Password']."</td>";
            echo "<td>".$fila['Rol']."</td>";

            echo "</tr>";
        }

        ?>

    </table>

    <br><br>

    <a href="index.php">⬅ Volver al inicio</a>

</div>

</body>
</html>