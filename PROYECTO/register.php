<?php
include 'includes/db.php';

if ($_POST) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    if ($stmt->execute([$username, $password])) {
        header('Location: login.php');
    } else {
        echo "Error al registrar.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Registro</title>
    <link rel="stylesheet" href="../CSS/register.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DynaPuff:wght@400..700&family=Knewave&display=swap" rel="stylesheet">
</head>
<body class="container">
    <div class="containers">
       <h2>Registro de Usuario</h2>
    <form method="POST">
        <input name="username" class="form-control" placeholder="Usuario" required><br>
        <input name="password" type="password" class="form-control" placeholder="Contraseña" required><br>
        <button class="btn btn-primary">Registrar</button>
    </form>
    <a  href="login.php">Iniciar sesión</a>
    </div>

</body>
</html>
