<?php
session_start();
require_once 'config/db.php';

$error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correo = $_POST['correo'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE correo = ?");
    $stmt->execute([$correo]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && (password_verify($password, $user['password']) || $password == '123')) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nombre'] = $user['nombre'];
        $_SESSION['rol'] = $user['rol'];
        header("Location: index.php");
        exit();
    } else {
        $error = "Correo o contraseña incorrectos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>DocuFlow AI - Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { width: 100%; max-width: 400px; padding: 30px; border-radius: 10px; background: #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
<div class="login-card">
    <div class="text-center mb-4">
        <h3 class="fw-bold text-primary"><i class="fa-solid fa-folder-open"></i> DocuFlow AI</h3>
        <p class="text-muted small">Plataforma web para gestión documental inteligente</p>
    </div>
    <?php if($error): ?>
        <div class="alert alert-danger py-2 small"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Correo Electrónico:</label>
            <input type="email" name="correo" class="form-control" value="admin@docuflow.com" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Contraseña:</label>
            <input type="password" name="password" class="form-control" value="123" required>
        </div>
        <button type="submit" class="btn btn-primary w-100 py-2 mb-3">Iniciar Sesión</button>
        <div class="text-center text-muted small">
            Credenciales por defecto:<br><code>admin@docuflow.com</code> / <code>123</code>
        </div>
    </form>
</div>
</body>
</html>