<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';
$citas = [];

// Verificar si se ha enviado el formulario para crear una cita
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fecha_cita = $_POST['fecha_cita'];
    $motivo_cita = $_POST['motivo_cita'];
    $idUser = $_SESSION['idUser']; // Obtener el idUser desde la sesión

    // Validar que los campos no estén vacíos
    if (empty($fecha_cita) || empty($motivo_cita)) {
        $error = "Por favor, completa todos los campos.";
    } else {
        // Preparar los datos para enviar al servicio
        $data = array(
            "idUser" => $idUser,
            "fecha_cita" => $fecha_cita,
            "motivo_cita" => $motivo_cita
        );

        // Convertir los datos a JSON
        $jsonData = json_encode($data);

        // Hacer la solicitud POST al servicio para crear la cita
        $api_url = 'http://localhost:3000/api/citas';
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
        if (isset($result['message']) && $result['message'] === "Cita creada exitosamente") {
            $success = "Cita creada exitosamente.";
        } else {
            $error = "Hubo un problema al crear la cita.";
        }
    }
}

// Obtener la lista de citas
$api_url = 'http://localhost:3000/api/citas';
$response = file_get_contents($api_url);
$citas = json_decode($response, true); // Decodificar la respuesta JSON en un array

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Citas</title>
    <link rel="stylesheet" href="src/css/citas.css"> <!-- Archivo CSS -->
</head>
<body>
    <nav>
        <ul>
            <li><a href="index.php">Inicio</a></li>
            <li><a href="noticias.php">Noticias</a></li>
            <li><a href="citas.php" class="active">Citas</a></li>
            <li><a href="logout.php">Cerrar Sesión</a></li>
        </ul>
    </nav>

    <section class="citas">
        <h1>Mis Citas</h1>

        <?php if (!empty($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php elseif (!empty($success)): ?>
            <p class="success"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>

        <!-- Formulario para crear una nueva cita -->
        <form action="citas.php" method="POST">
            <div class="form-group">
                <label for="fecha_cita">Fecha de la Cita:</label>
                <input type="date" id="fecha_cita" name="fecha_cita" required>
            </div>
            <div class="form-group">
                <label for="motivo_cita">Motivo de la Cita:</label>
                <input type="text" id="motivo_cita" name="motivo_cita" required>
            </div>
            <button type="submit">Crear Cita</button>
        </form>

        <!-- Listado de citas -->
        <?php if (!empty($citas)): ?>
            <h2>Citas Programadas</h2>
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Motivo</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($citas as $cita): ?>
                        <tr>
                            <td><?= htmlspecialchars($cita['fecha_cita']) ?></td>
                            <td><?= htmlspecialchars($cita['motivo_cita']) ?></td>
                            <td><?= htmlspecialchars($cita['estado'] ?? 'Pendiente') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No tienes citas programadas.</p>
        <?php endif; ?>
    </section>
</body>
</html>
