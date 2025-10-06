<?php

$DBServerName = "localhost";
$DBUserName   = "root"; 
$DBPassword   = "";    
$DBName       = "foodorder_db"; 

$conn = mysqli_connect($DBServerName, $DBUserName, $DBPassword, $DBName);
if (!$conn) die("Connection failed: " . mysqli_connect_error());

$current_user_id = 1;

if (isset($_POST['remove_item'])) {
    $cart_id = mysqli_real_escape_string($conn, $_POST['cart_id']);
    $sql = "DELETE FROM cart WHERE id = '$cart_id' AND user_id = '$current_user_id' LIMIT 1";
    if ($conn->query($sql)) {
    } else {
        echo "<p class='msg error'>❌ Error removing item: " . $conn->error . "</p>";
    }
}

if (isset($_POST['update_quantity'])) {
    $cart_id  = mysqli_real_escape_string($conn, $_POST['cart_id']);
    $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);

    if ($quantity < 1) $quantity = 1;

    $sql = "UPDATE cart SET quantity = '$quantity' WHERE id = '$cart_id' AND user_id = '$current_user_id' LIMIT 1";
    if ($conn->query($sql)) {
    } else {
        echo "<p class='msg error'>❌ Error updating quantity: " . $conn->error . "</p>";
    }
}

$sql = "SELECT c.id AS cart_id, c.quantity, m.name, m.price, m.image 
        FROM cart c
        JOIN menu_items m ON c.menu_item_id = m.id
        WHERE c.user_id = '$current_user_id'";
        
$result = $conn->query($sql);

$total = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Cart - FoodOrder</title>
    <link rel="stylesheet" href="cart.css" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&display=swap" rel="stylesheet" />
</head>
<body>
    <nav class="nav">
        <a href="index.html" class="link">Home</a>
        <a href="order.php" class="link">Menu</a>
        <a href="aboutus.html" class="link">Contact</a>
    </nav>

    <div class="cart-container">
        <h1>Your Cart</h1>
        <div id="cartItems">
        <?php if ($result->num_rows > 0) {
            while ($item = $result->fetch_assoc()) {
                $item_total = $item['price'] * $item['quantity'];
                $total += $item_total;
        ?>
          <div class="cart-item">
            <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" width="200" height="100" />
            <div class="item-details">
              <h3><?= htmlspecialchars($item['name']) ?></h3>
              <p>Price: ₹<?= number_format($item['price'], 2) ?></p>
              <form method="POST" class="quantity-form">
                <input type="hidden" name="cart_id" value="<?= htmlspecialchars($item['cart_id']) ?>">
                <label for="quantity-<?= $item['cart_id'] ?>">Quantity:</label>
                <input type="number" name="quantity" value="<?= htmlspecialchars($item['quantity']) ?>" min="1" onchange="this.form.submit()">
                <input type="hidden" name="update_quantity" value="1">
              </form>
              <form method="POST" class="remove-form">
                <input type="hidden" name="cart_id" value="<?= htmlspecialchars($item['cart_id']) ?>">
                <button type="submit" name="remove_item" class="remove-btn">Remove</button>
              </form>
            </div>
          </div>
        <?php 
            } 
        } else {
            echo "<p>Your cart is empty.</p>";
        }
        ?>
        </div>

        <div class="cart-total">
            <h2>Total: ₹<?= number_format($total, 2) ?></h2>
            <button class="checkout-btn">
                <a href="payment.html">Checkout</a>
            </button>
        </div>
    </div>

    <footer>
        <p>© 2025 YourWebsite. All rights reserved.</p>
    </footer>

</body>
</html>
<?php $conn->close(); ?>