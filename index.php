<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$totalDocs = $pdo->query("SELECT COUNT(*) FROM documentos")->fetchColumn();
$documentos = $pdo->query("SELECT * FROM documentos ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>DocuFlow AI - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; }
        .sidebar { min-height: 100vh; background: #212529; color: #fff; }
        .sidebar a { color: #adb5bd; text-decoration: none; display: block; padding: 10px 15px; border-radius: 5px; margin-bottom: 5px; }
        .sidebar a:hover, .sidebar a.active { background: #0d6efd; color: #fff; }
        .card-stat { border-left: 4px solid #0d6efd; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 sidebar p-3 d-flex flex-column justify-content-between">
            <div>
                <h4 class="text-white mb-4"><i class="fa-solid fa-folder-open text-primary"></i> DocuFlow AI</h4>
                <a href="index.php" class="active"><i class="fa-solid fa-chart-line me-2"></i> Dashboard</a>
                <a href="upload.php"><i class="fa-solid fa-cloud-arrow-up me-2"></i> Cargar con IA</a>
                <a href="buscar.php"><i class="fa-solid fa-magnifying-glass me-2"></i> Búsqueda & RAG</a>
                <a href="audit.php"><i class="fa-solid fa-shield-halved me-2"></i> Auditoría</a>
            </div>
            <div>
                <hr class="text-secondary">
                <div class="small text-muted px-2 mb-2">Usuario:<br><strong><?php echo htmlspecialchars($_SESSION['nombre']); ?></strong></div>
                <a href="logout.php" class="text-danger"><i class="fa-solid fa-right-from-bracket me-2"></i> Cerrar Sesión</a>
            </div>
        </div>

        <!-- Contenido -->
        <div class="col-md-9 col-lg-10 px-4 py-4">
            <h2 class="mb-4">Panel de Control General</h2>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card card-stat shadow-sm p-3 bg-white">
                        <h6 class="text-muted">Total Documentos</h6>
                        <h3><?php echo $totalDocs; ?></h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-stat shadow-sm p-3 bg-white border-success">
                        <h6 class="text-muted">Estado del Sistema</h6>
                        <h3 class="text-success">Operativo ⚡</h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-stat shadow-sm p-3 bg-white border-warning">
                        <h6 class="text-muted">Modelo IA Activo</h6>
                        <h3 class="text-primary">Multimodal / OCR</h3>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm p-4 bg-white">
                <h5>Documentos Recientes</h5>
                <div class="table-responsive mt-3">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Código</th>
                                <th>Tipo</th>
                                <th>Asunto</th>
                                <th>Remitente</th>
                                <th>Fecha</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count($documentos) > 0): foreach($documentos as $d): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($d['codigo_doc']); ?></strong></td>
                                <td><?php echo htmlspecialchars($d['tipo_documento']); ?></td>
                                <td><?php echo htmlspecialchars($d['asunto']); ?></td>
                                <td><?php echo htmlspecialchars($d['remitente']); ?></td>
                                <td><?php echo htmlspecialchars($d['fecha_emision']); ?></td>
                                <td><a href="<?php echo htmlspecialchars($d['ruta_archivo']); ?>" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-eye"></i></a></td>
                            </tr>
                            <?php endforeach; else: ?>
                            <tr><td colspan="6" class="text-center text-muted">No hay documentos registrados.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>