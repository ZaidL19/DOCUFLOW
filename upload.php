<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$mensaje = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $codigo = $_POST['codigo_doc'];
    $tipo = $_POST['tipo_documento'];
    $asunto = $_POST['asunto'];
    $remitente = $_POST['remitente'];
    $destinatario = $_POST['destinatario'];
    $categoria = $_POST['categoria'];
    $fecha = $_POST['fecha_emision'];
    $palabras_clave = $_POST['palabras_clave'];
    $user_id = $_SESSION['user_id'];

    if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] == 0) {
        $dir = 'uploads/';
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        $rutaDestino = $dir . time() . '_' . basename($_FILES['archivo']['name']);
        
        if (move_uploaded_file($_FILES['archivo']['tmp_name'], $rutaDestino)) {
            $stmt = $pdo->prepare("INSERT INTO documentos (codigo_doc, tipo_documento, asunto, remitente, destinatario, categoria, fecha_emision, palabras_clave, ruta_archivo, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$codigo, $tipo, $asunto, $remitente, $destinatario, $categoria, $fecha, $palabras_clave, $rutaDestino, $user_id]);
            $doc_id = $pdo->lastInsertId();

            $pdo->prepare("INSERT INTO historial_auditoria (documento_id, usuario_id, accion, detalles) VALUES (?, ?, 'CARGA_IA', ?)")
                ->execute([$doc_id, $user_id, "Documento procesado por IA y registrado con éxito."]);

            $mensaje = "¡Documento procesado por IA y guardado correctamente!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>DocuFlow AI - Carga IA</title>
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
                <a href="upload.php" class="active"><i class="fa-solid fa-cloud-arrow-up me-2"></i> Cargar con IA</a>
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
            <h2 class="mb-4">Carga y Procesamiento Inteligente (IA / OCR)</h2>
            <?php if($mensaje): ?><div class="alert alert-success"><?php echo $mensaje; ?></div><?php endif; ?>
            
            <div class="card shadow-sm p-4 bg-white">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Seleccionar Archivo (PDF, Imagen):</label>
                        <input type="file" name="archivo" class="form-control" required>
                    </div>
                    <button type="button" onclick="simularExtraccionIA()" class="btn btn-primary mb-4">
                        <i class="fa-solid fa-wand-magic-sparkles me-2"></i> Simular Extracción Automática por IA
                    </button>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Código del Documento:</label>
                            <input type="text" name="codigo_doc" id="codigo_doc" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tipo de Documento:</label>
                            <select name="tipo_documento" id="tipo_documento" class="form-select">
                                <option value="Oficio">Oficio</option>
                                <option value="Contrato">Contrato</option>
                                <option value="Memorando">Memorando</option>
                                <option value="Resolución">Resolución</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Asunto:</label>
                        <textarea name="asunto" id="asunto" class="form-control" rows="2" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Remitente:</label>
                            <input type="text" name="remitente" id="remitente" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Destinatario:</label>
                            <input type="text" name="destinatario" id="destinatario" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Categoría:</label>
                            <select name="categoria" id="categoria" class="form-select">
                                <option value="Administrativo">Administrativo</option>
                                <option value="Legal">Legal</option>
                                <option value="Financiero">Financiero</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Fecha de Emisión:</label>
                            <input type="date" name="fecha_emision" id="fecha_emision" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Palabras Clave:</label>
                            <input type="text" name="palabras_clave" id="palabras_clave" class="form-control" placeholder="ej. personal, contratos">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success w-100"><i class="fa-solid fa-floppy-disk me-2"></i> Confirmar y Guardar Documento</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
function simularExtraccionIA() {
    document.getElementById('codigo_doc').value = "OFC-2026-778";
    document.getElementById('asunto').value = "Informe técnico sobre la implementación del módulo de seguridad y accesos.";
    document.getElementById('remitente').value = "Oficina de Tecnologías de Información";
    document.getElementById('destinatario').value = "Dirección Ejecutiva";
    document.getElementById('fecha_emision').value = "2026-10-21";
    document.getElementById('palabras_clave').value = "seguridad, accesos, informatica";
    alert("⚡ ¡IA procesada correctamente! Metadatos extraídos automáticamente.");
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>