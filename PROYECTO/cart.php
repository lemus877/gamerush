<?php
include 'includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_POST && isset($_POST['update'])) {
    foreach ($_POST['quantity'] as $cart_id => $qty) {
        $qty = max(1, intval($qty));
        $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$qty, $cart_id, $user_id]);
    }
}

if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $stmt->execute([$_GET['delete'], $user_id]);
}

$stmt = $pdo->prepare("
    SELECT c.id, p.name, p.price, p.image, c.quantity
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
");
$stmt->execute([$user_id]);
$items = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Carrito</title>
    <link rel="stylesheet" href="../CSS/cart.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DynaPuff:wght@400..700&family=Knewave&display=swap" rel="stylesheet">
    <style>
        img { width: 80px; height: 80px; object-fit: cover; }
    </style>
</head>
<body class="container">
    <h2>Mi Carrito</h2>
    <a href="index.php" class="btn btn-secondary">Volver</a>
    <form method="POST">
        <table class="table table-bordered">
            <tr>
                <th>Imagen</th>
                <th>Producto</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Subtotal</th>
                <th>Acciones</th>
            </tr>
            <?php
           
            $total = 0;
            foreach ($items as $item):
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;
            ?>
            <tr>
                <td>
                    <?php if ($item['image']): ?>
                        <img src="assets/<?= htmlspecialchars($item['image']) ?>" alt="">
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td>$<?= number_format($item['price'], 2) ?></td>
                <td>
                    <input type="number" name="quantity[<?= $item['id'] ?>]" value="<?= $item['quantity'] ?>" min="1" class="form-control" style="width:80px;">
                </td>
                <td>$<?= number_format($subtotal, 2) ?></td>
                <td>
                    <a href="cart.php?delete=<?= $item['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este producto?');">Eliminar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

        <h4>Total: $<?= number_format($total, 2) ?></h4>

        <?php if (count($items) > 0): ?>
            <button name="update" class="btn btn-primary">Actualizar cantidades</button>
        <?php else: ?>
            <p>No tienes productos en tu carrito.</p>
        <?php endif; ?>
    </form>
</body>
</html>
