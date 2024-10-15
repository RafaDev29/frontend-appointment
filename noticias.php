<?php
session_start(); // Iniciar sesión para verificar si el usuario está autenticado

// URL del endpoint de noticias
$api_url = 'http://localhost:3000/api/noticias';

// Hacer una solicitud HTTP GET para obtener las noticias
$response = file_get_contents($api_url);
$noticias = json_decode($response, true); // Decodificar la respuesta JSON a un array asociativo
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Noticias</title>
    <link rel="stylesheet" href="src/css/noticias.css"> <!-- Archivo CSS -->
</head>
<body>
    <nav>
        <ul>
            <li><a href="index.php">Inicio</a></li>
            <li><a href="noticias.php" class="active">Noticias</a></li>
            <?php if (isset($_SESSION['usuario'])): ?> <!-- Si el usuario ha iniciado sesión -->
                <li><a href="citas.php">Citas</a></li>
                <li><a href="logout.php">Cerrar Sesión</a></li>
            <?php else: ?>
                <li><a href="registro.php">Registro</a></li>
                <li><a href="login.php">Iniciar Sesión</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <section class="noticias">
        <h1>Últimas Noticias</h1>
        <?php if (!empty($noticias)): ?>
            <?php foreach ($noticias as $noticia): ?>
                <article class="noticia">
                    <h2><?= htmlspecialchars($noticia['titulo']) ?></h2>
                    <p class="fecha">Publicado el: <?= htmlspecialchars($noticia['fecha']) ?> por <?= htmlspecialchars($noticia['nombreUsuario'] . ' ' . $noticia['apellidosUsuario']) ?></p>
                    <img src="<?= htmlspecialchars($noticia['imagen']) ?>" alt="<?= htmlspecialchars($noticia['titulo']) ?>" style="max-width: 100%; height: auto;">
                    <p><?= nl2br(htmlspecialchars($noticia['texto'])) ?></p>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No hay noticias disponibles en este momento.</p>
        <?php endif; ?>
    </section>
</body>
</html>
