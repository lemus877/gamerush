<?php
include 'includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Agregar producto
if ($_POST) {
    // AGREGAR
    if (isset($_POST['add'])) {
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] == UPLOAD_ERR_OK) {
            $img_name = basename($_FILES['image_file']['name']);
            $target_dir = 'assets/';
            $target_file = $target_dir . $img_name;

            // Mover la imagen subida al directorio assets
            if (move_uploaded_file($_FILES['image_file']['tmp_name'], $target_file)) {
                $stmt = $pdo->prepare("INSERT INTO products (name, description, price, image) VALUES (?, ?, ?, ?)");
                $stmt->execute([$_POST['name'], $_POST['desc'], $_POST['price'], $img_name]);
                $msg = "Producto agregado exitosamente.";
            } else {
                $msg = "Error al subir la imagen.";
            }
        } else {
            $msg = "Debe seleccionar una imagen válida.";
        }
    }

    // ELIMINAR
    if (isset($_POST['delete'])) {
        $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
        $stmt->execute([$_POST['delete']]);
        $product = $stmt->fetch();
        if ($product && file_exists('assets/' . $product['image'])) {
            unlink('assets/' . $product['image']); // eliminar archivo
        }

        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$_POST['delete']]);
        $msg = "Producto eliminado.";
    }

    // EDITAR
    if (isset($_POST['edit'])) {
        $img_name = $_POST['old_image']; // por defecto mantiene la imagen anterior
        if (isset($_FILES['image_edit']) && $_FILES['image_edit']['error'] == UPLOAD_ERR_OK) {
            $img_name = basename($_FILES['image_edit']['name']);
            $target_dir = 'assets/';
            $target_file = $target_dir . $img_name;
            if (move_uploaded_file($_FILES['image_edit']['tmp_name'], $target_file)) {
                // eliminar imagen anterior si existe
                if (file_exists('assets/' . $_POST['old_image'])) {
                    unlink('assets/' . $_POST['old_image']);
                }
            } else {
                $img_name = $_POST['old_image']; // si falla, mantiene anterior
            }
        }

        $stmt = $pdo->prepare("UPDATE products SET name=?, description=?, price=?, image=? WHERE id=?");
        $stmt->execute([$_POST['name'], $_POST['desc'], $_POST['price'], $img_name, $_POST['edit']]);
        $msg = "Producto actualizado.";
    }
}

$products = $pdo->query("SELECT * FROM products")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>CRUD Productos</title>
    <link rel="stylesheet" href="../CSS/crud.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DynaPuff:wght@400..700&family=Knewave&display=swap" rel="stylesheet">
    <style>
        img { height: 80px; object-fit: cover; }
    </style>
</head>
<body class="container">
    <h2>Gestión de Productos</h2>
    <a href="index.php" class="btn btn-secondary">Volver a Productos</a>
    <?php if (isset($msg)): ?>
        <div class="alert alert-info"><?= $msg ?></div>
    <?php endif; ?>

    <h4>Agregar Nuevo Producto</h4>
    <form method="POST" enctype="multipart/form-data" class="mb-4">
        <input name="name" placeholder="Nombre" required class="form-control mb-2">
        <input name="desc" placeholder="Descripción" required class="form-control mb-2">
        <input name="price" type="number" step="0.01" placeholder="Precio" required class="form-control mb-2">
        <input name="image_file" type="file" required class="form-control mb-2">
        <button name="add" class="btn btn-primary">Agregar Producto</button>
    </form>

    <h4>Productos Existentes</h4>
    <table class="table table-bordered">
        <tr>
            <th>ID</th><th>Imagen</th><th>Nombre</th><th>Descripción</th><th>Precio</th><th>Acciones</th>
        </tr>
        <?php foreach ($products as $p): ?>
        <tr>
            <form method="POST" enctype="multipart/form-data">
                <td><?= $p['id'] ?><input type="hidden" name="edit" value="<?= $p['id'] ?>"></td>
                <td>
                    <?php if ($p['image']): ?>
                        <img src="assets/<?= htmlspecialchars($p['image']) ?>">
                    <?php endif; ?>
                    <input type="hidden" name="old_image" value="<?= htmlspecialchars($p['image']) ?>">
                    <input type="file" name="image_edit" class="form-control">
                </td>
                <td><input name="name" value="<?= htmlspecialchars($p['name']) ?>" class="form-control"></td>
                <td><input name="desc" value="<?= htmlspecialchars($p['description']) ?>" class="form-control"></td>
                <td><input name="price" type="number" step="0.01" value="<?= $p['price'] ?>" class="form-control"></td>
                <td>
                    <button class="btn btn-success btn-sm">Guardar</button><br><br>
                    <button name="delete" value="<?= $p['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar producto?');">Eliminar</button>
                </td>
            </form>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
