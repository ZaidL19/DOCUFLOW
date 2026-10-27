<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Búsqueda convencional
$filtro = isset($_GET['busqueda']) ? '%' . $_GET['busqueda'] . '%' : '%';
$stmt = $pdo->prepare("SELECT * FROM documentos WHERE asunto LIKE ? OR remitente LIKE ? OR codigo_doc LIKE ? ORDER BY id DESC");
$stmt->execute([$filtro, $filtro, $filtro]);
$documentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>DocuFlow AI - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark px-4">
        <span class="navbar-brand mb-0 h1">DocuFlow AI - Panel Principal</span>
        <a href="logout.php" class="btn btn-outline-light btn-sm">Cerrar Sesión</a>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>Gestión y Búsqueda de Documentos</h4>
            <a href="upload.php" class="btn btn-primary">+ Cargar Nuevo Documento</a>
        </div>

        <form method="GET" class="input-group mb-4">
            <input type="text" name="busqueda" class="form-control" placeholder="Buscar por código, remitente o asunto..." value="<?= htmlspecialchars($_GET['busqueda'] ?? '') ?>">
            <button class="btn btn-outline-secondary" type="submit">Buscar</button>
        </form>

        <div class="card shadow-sm p-3">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Tipo</th>
                        <th>Asunto</th>
                        <th>Remitente</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($documentos) > 0): ?>
                        <?php foreach($documentos as $doc): ?>
                        <tr>
                            <td><?= htmlspecialchars($doc['codigo_doc']) ?></td>
                            <td><?= htmlspecialchars($doc['tipo_documento']) ?></td>
                            <td><?= htmlspecialchars($doc['asunto']) ?></td>
                            <td><?= htmlspecialchars($doc['remitente']) ?></td>
                            <td><?= htmlspecialchars($doc['fecha_emision']) ?></td>
                            <td><a href="<?= htmlspecialchars($doc['ruta_archivo']) ?>" target="_blank" class="btn btn-sm btn-info">Ver Archivo</a></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center">No se encontraron documentos registrados.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>