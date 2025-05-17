<?php
include 'includes/db.php';
session_start();

if ($_POST) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: index.php');
    } else {
        echo "Usuario o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Iniciar Sesión</title>
      <link rel="stylesheet" href="../CSS/login.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DynaPuff:wght@400..700&family=Knewave&display=swap" rel="stylesheet">
</head>
<body class="container">
     <nav class="barra_navegacion" id="barra_navegacion">
             <a class="inicio_sesion" href="Inicio.html">Inicio</a>
            <a class="inicio_sesion" href="login.php">INICIO DE SESION</a>
            <a class="registro" href="register.php">REGISTRO</a>
        </nav>
    <h2 class=>Iniciar Sesión</h2>
    <form method="POST">
        <input name="username" class="form-control" placeholder="Usuario" required><br>
        <input name="password" type="password" class="form-control" placeholder="Contraseña" required><br>
        <button class="btn btn-primary">Entrar</button>
    </form>
    <a href="register.php">Registrarse</a>
</body>
</html>
