<?php

session_start();

require_once "api/conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit();
}

$correo = trim($_POST["correo"] ?? "");
$password = $_POST["password"] ?? "";

if ($correo === "" || $password === "") {
    header("Location: login.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| Buscar solamente por correo
|--------------------------------------------------------------------------
*/

$sql = "SELECT IdUsuario, Nombre, Correo, Password, Rol
        FROM Usuarios
        WHERE Correo = ?";

$params = [$correo];

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

$fila = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Verificar la contraseña escrita contra el hash almacenado
|--------------------------------------------------------------------------
*/

if ($fila && password_verify($password, $fila["Password"])) {

    $_SESSION["idUsuario"] = $fila["IdUsuario"];
    $_SESSION["nombre"] = $fila["Nombre"];
    $_SESSION["rol"] = $fila["Rol"];
    $_SESSION["correo"] = $fila["Correo"];

    $nombreSeguro = htmlspecialchars(
        $fila["Nombre"],
        ENT_QUOTES,
        "UTF-8"
    );

    echo "
    <!DOCTYPE html>
    <html lang='es'>

    <head>
        <meta charset='UTF-8'>
        <meta name='viewport'
              content='width=device-width, initial-scale=1.0'>

        <meta http-equiv='refresh'
              content='2;url=index.php'>

        <link rel='stylesheet'
              href='style.css?v=3'>

        <title>Bienvenido</title>
    </head>

    <body class='login-body'>

        <div class='login-container'>

            <h1>✅</h1>

            <h2>Bienvenido</h2>

            <h3>{$nombreSeguro}</h3>

            <p>Inicio de sesión correcto.</p>

            <p>Redirigiendo al sistema...</p>

        </div>

    </body>

    </html>
    ";

} else {

    echo "
    <!DOCTYPE html>
    <html lang='es'>

    <head>
        <meta charset='UTF-8'>
        <meta name='viewport'
              content='width=device-width, initial-scale=1.0'>

        <meta http-equiv='refresh'
              content='3;url=login.php'>

        <link rel='stylesheet'
              href='style.css?v=3'>

        <title>Acceso denegado</title>
    </head>

    <body class='login-body'>

        <div class='login-container'>

            <h1>❌</h1>

            <h2>Usuario o contraseña incorrectos</h2>

            <p>Volviendo al inicio...</p>

        </div>

    </body>

    </html>
    ";
}