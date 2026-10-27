<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$auditorias = $pdo->query("SELECT a.*, u.nombre as usuario, d.codigo_doc FROM historial_auditoria a JOIN usuarios u ON a.usuario_id = u.id LEFT JOIN documentos d ON a.documento_id = d.id ORDER BY a.id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>DocuFlow AI - Auditoría</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; }
        .sidebar { min-height: 100vh; background: #212529; color: #fff; }
        .sidebar a { color: #adb5bd; text-decoration: none; display: block; padding: 10px 15px; border-radius: 5px; margin-bottom: 5px; }
        .sidebar a:hover, .sidebar a.active { background: #0d6efd; color: #fff; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 sidebar p-3 d-flex flex-column justify-content-between">
            <div>
                <h4 class="text-white mb-4"><i class="fa-solid fa-folder-open text-primary"></i> DocuFlow AI</h4>
                <a href="index.php"><i class="fa-solid fa-chart-line me-2"></i> Dashboard</a>
                <a href="upload.php"><i class="fa-solid fa-cloud-arrow-up me-2"></i> Cargar con IA</a>
                <a href="buscar.php"><i class="fa-solid fa-magnifying-glass me-2"></i> Búsqueda & RAG</a>
                <a href="audit.php" class="active"><i class="fa-solid fa-shield-halved me-2"></i> Auditoría</a>
            </div>
            <div>
                <hr class="text-secondary">
                <div class="small text-muted px-2 mb-2">Usuario:<br><strong><?php echo htmlspecialchars($_SESSION['nombre']); ?></strong></div>
                <a href="logout.php" class="text-danger"><i class="fa-solid fa-right-from-bracket me-2"></i> Cerrar Sesión</a>
            </div>
        </div>

        <!-- Contenido -->
        <div class="col-md-9 col-lg-10 px-4 py-4">
            <h2 class="mb-4">Historial de Auditoría y Trazabilidad</h2>
            <div class="card shadow-sm p-3 bg-white">
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Acción</th>
                            <th>Documento</th>
                            <th>Detalles</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($auditorias as $aud): ?>
                        <tr>
                            <td><?php echo $aud['id']; ?></td>
                            <td><?php echo htmlspecialchars($aud['usuario']); ?></td>
                            <td><span class="badge bg-dark"><?php echo htmlspecialchars($aud['accion']); ?></span></td>
                            <td><?php echo htmlspecialchars($aud['codigo_doc'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($aud['detalles']); ?></td>
                            <td><?php echo $aud['fecha_hora']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>