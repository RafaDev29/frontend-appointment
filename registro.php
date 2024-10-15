<?php
$error = '';
$success = '';

// Si el formulario se ha enviado, procesamos los datos
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $direccion = $_POST['direccion'];
    $sexo = $_POST['sexo'];
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    // Validar que los campos no estén vacíos
    if (empty($nombre) || empty($apellidos) || empty($email) || empty($telefono) || empty($fecha_nacimiento) || empty($usuario) || empty($password)) {
        $error = "Por favor, completa todos los campos obligatorios.";
    } else {
        // Preparar los datos para enviar al servicio
        $data = array(
            "nombre" => $nombre,
            "apellidos" => $apellidos,
            "email" => $email,
            "telefono" => $telefono,
            "fecha_nacimiento" => $fecha_nacimiento,
            "direccion" => $direccion,
            "sexo" => $sexo,
            "usuario" => $usuario,
            "password" => $password,
            "rol" => "user" // Rol por defecto
        );

        // Convertir los datos a JSON
        $jsonData = json_encode($data);

        // Hacer la solicitud POST al servicio
        $api_url = 'http://localhost:3000/api/users';
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
        if (isset($result['message']) && $result['message'] === "User created successfully") {
            // Usuario registrado con éxito
            $success = "Usuario registrado exitosamente. Redirigiendo al login...";
            header("refresh:3;url=login.php"); // Redirigir a login después de 3 segundos
        } else {
            // Error en el registro (por ejemplo, si el usuario ya está registrado)
            $error = "El usuario ya ha sido registrado o hubo un error en el registro.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="src/css/registro.css"> <!-- Archivo CSS -->
</head>
<body>
    <nav>
        <ul>
            <li><a href="index.php">Inicio</a></li>
            <li><a href="noticias.php">Noticias</a></li>
            <li><a href="registro.php" class="active">Registro</a></li>
            <li><a href="login.php">Iniciar Sesión</a></li>
        </ul>
    </nav>

    <section class="registro">
        <h1>Registro de Usuario</h1>
        <?php if (!empty($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php elseif (!empty($success)): ?>
            <p class="success"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>
        
        <form action="registro.php" method="POST">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>
            <div class="form-group">
                <label for="apellidos">Apellidos:</label>
                <input type="text" id="apellidos" name="apellidos" required>
            </div>
            <div class="form-group">
                <label for="email">Correo Electrónico:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" name="telefono" required>
            </div>
            <div class="form-group">
                <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required>
            </div>
            <div class="form-group">
                <label for="direccion">Dirección:</label>
                <input type="text" id="direccion" name="direccion">
            </div>
            <div class="form-group">
                <label for="sexo">Sexo:</label>
                <select id="sexo" name="sexo">
                    <option value="M">Masculino</option>
                    <option value="F">Femenino</option>
                    <option value="O">Otro</option>
                </select>
            </div>
            <div class="form-group">
                <label for="usuario">Usuario:</label>
                <input type="text" id="usuario" name="usuario" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Registrar</button>
        </form>
    </section>
</body>
</html>
