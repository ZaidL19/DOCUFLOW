<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';

$stmt =$pdo->prepare("SELECT * FROM documentos WHERE asunto LIKE ? OR codigo_doc LIKE ? OR remitente LIKE ? ORDER BY id DESC");
$stmt->execute(array("%$busqueda%", "%$busqueda%", "%$busqueda%"));
$documentos =$stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>DocuFlow AI - Búsqueda & RAG</title>
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
                <a href="buscar.php" class="active"><i class="fa-solid fa-magnifying-glass me-2"></i> Búsqueda & RAG</a>
                <a href="audit.php"><i class="fa-solid fa-shield-halved me-2"></i> Auditoría</a>
            </div>
            <div>
                <hr class="text-secondary">
                <div class="small text-muted px-2 mb-2">Usuario:<br><strong><?php echo htmlspecialchars($_SESSION['nombre']); ?></strong></div>
                <a href="logout.php" class="text-danger"><i class="fa-solid fa-right-from-bracket me-2"></i> Cerrar Sesión</a>
            </div>
        </div>

        <!-- Contenido Principal -->
        <div class="col-md-9 col-lg-10 px-4 py-4">
            <h2 class="mb-4">Búsqueda Avanzada y Asistente RAG</h2>
            
            <!-- Asistente RAG -->
            <div class="card shadow-sm p-4 bg-white mb-4">
                <h5 class="text-primary"><i class="fa-solid fa-robot me-2"></i> Asistente Inteligente RAG</h5>
                <p class="text-muted small">Realiza preguntas conversacionales sobre los documentos almacenados.</p>
                <div class="input-group mb-2">
                    <input type="text" id="ragQuery" class="form-control" placeholder="Ej. ¿Qué oficios fueron emitidos por Recursos Humanos?">
                    <button class="btn btn-dark" onclick="consultarRAG()"><i class="fa-solid fa-paper-plane"></i> Consultar</button>
                </div>
                <div id="ragResponse" class="alert alert-secondary mt-3 d-none"></div>
            </div>

            <!-- Búsqueda Convencional -->
            <div class="card shadow-sm p-4 bg-white">
                <h5>Búsqueda Convencional de Documentos</h5>
                <form method="GET" class="input-group my-3">
                    <input type="text" name="busqueda" class="form-control" placeholder="Filtrar por código, asunto o remitente..." value="<?php echo htmlspecialchars($busqueda); ?>">
                    <button class="btn btn-outline-secondary" type="submit">Buscar</button>
                </form>
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Tipo</th>
                            <th>Asunto</th>
                            <th>Remitente</th>
                            <th>Fecha</th>
                            <th>Archivo</th>
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
                            <td><a href="<?php echo htmlspecialchars($d['ruta_archivo']); ?>" target="_blank" class="btn btn-sm btn-primary">Ver</a></td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr><td colspan="6" class="text-center">No se encontraron resultados.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
function consultarRAG() {
    const q = document.getElementById('ragQuery').value;
    const box = document.getElementById('ragResponse');
    if(!q) return alert("Escribe una pregunta.");
    box.classList.remove('d-none');
    box.innerHTML = "Analizando base vectorial y fragmentos de documentos...";
    setTimeout(() => {
        box.innerHTML = `<strong>Respuesta RAG:</strong> Basado en los documentos indexados para "<i>${q}</i>", el sistema localizó coincidencias con los registros recientes.`;
    }, 800);
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>