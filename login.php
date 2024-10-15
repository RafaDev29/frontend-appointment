<?php
session_start(); // Iniciar sesión para verificar si el usuario está autenticado

$error = '';

// Si el formulario se ha enviado, procesamos los datos
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    // Validar que los campos no estén vacíos
    if (empty($usuario) || empty($password)) {
        $error = "Por favor, completa todos los campos.";
    } else {
        // Preparar los datos para enviar al servicio
        $data = array(
            "usuario" => $usuario,
            "password" => $password
        );

        // Convertir los datos a JSON
        $jsonData = json_encode($data);

        // Hacer la solicitud POST al servicio
        $api_url = 'http://localhost:3000/api/users/login';
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'header'  => "Content-Type: application/json\r\n",
                'content' => $jsonData
            )
        );
        $context  = stream_context_create($options);
        $response = file_get_contents($api_url, false, $context);
        $result = json_decode($response, true);

        // Manejar la respuesta del servicio
        if (isset($result['message']) && $result['message'] === "Login successful") {
            // Inicio de sesión exitoso, guardar usuario y rol en la sesión
            $_SESSION['usuario'] = $result['user']['usuario'];
            $_SESSION['rol'] = $result['user']['rol'];
            $_SESSION['idUser'] = $result['user']['idUser'];
            // Redirigir al menú de citas
            header("Location: citas.php");
            exit();
        } else {
            // Usuario no registrado o error en el inicio de sesión
            $error = "El usuario no se encuentra registrado.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="src/css/login.css"> <!-- Archivo CSS -->
</head>
<body>
    <nav>
        <ul>
            <li><a href="index.php">Inicio</a></li>
            <li><a href="noticias.php">Noticias</a></li>
            <li><a href="registro.php">Registro</a></li>
            <li><a href="login.php" class="active">Iniciar Sesión</a></li>
            <?php if (isset($_SESSION['usuario'])): ?> <!-- Mostrar si está autenticado -->
                <li><a href="citas.php">Citas</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <section class="login">
        <h1>Iniciar Sesión</h1>
        <?php if (!empty($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="usuario">Usuario:</label>
                <input type="text" id="usuario" name="usuario" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Iniciar Sesión</button>
        </form>

        <p>¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>
    </section>
</body>
</html>
