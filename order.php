<?php
// Database connection details
$DBServerName = "localhost";
$DBUserName   = "root"; 
$DBPassword   = "";     
$DBName       = "foodorder_db"; 

$conn = mysqli_connect($DBServerName, $DBUserName, $DBPassword, $DBName);
if (!$conn) die("Connection failed: " . mysqli_connect_error());

// --- C. CREATE (Add to Cart) ---
if (isset($_POST['add_to_cart'])) {
    $menu_item_id = mysqli_real_escape_string($conn, $_POST['menu_item_id']);
    $user_id = 1; 
    $quantity = 1; 

    $check_sql = "SELECT id, quantity FROM cart WHERE user_id = '$user_id' AND menu_item_id = '$menu_item_id'";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        $row = $check_result->fetch_assoc();
        $new_quantity = $row['quantity'] + $quantity;
        $update_sql = "UPDATE cart SET quantity = '$new_quantity' WHERE id = '{$row['id']}'";
        $conn->query($update_sql);
    } else {
        $insert_sql = "INSERT INTO cart (user_id, menu_item_id, quantity) VALUES ('$user_id', '$menu_item_id', '$quantity')";
        $conn->query($insert_sql);
    }
    header("Location: cart.php");
    exit();
}

// Fetch all menu items from the database, grouped by category
$sql = "SELECT * FROM menu_items ORDER BY category, id";
$result = $conn->query($sql);
$menu_items_by_category = [];
while ($row = $result->fetch_assoc()) {
    $menu_items_by_category[$row['category']][] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Order</title>
    <link rel="stylesheet" href="order.css" />
</head>
<body>
    <nav class="nav">
        <a href="index.html" class="link">home</a>
        <a href="aboutus.html" class="link">about us</a>
        <a href="help.html" class="link">help</a>
    </nav>
    <br /><br />
    <center>
        <input type="text" placeholder="search" id="search" />
    </center>
    <div id="menuContainer"></div>

    <?php foreach ($menu_items_by_category as $category => $items) { ?>
        <table>
            <tr>
                <td class="td1"><h3><?= htmlspecialchars($category) ?></h3></td>
            </tr>
            <tr>
                <?php
                $item_count = 0;
                foreach ($items as $item) {
                    if ($item_count > 0 && $item_count % 4 == 0) {
                        echo "</tr><tr>";
                    }
                ?>
                    <td><img src="<?= htmlspecialchars($item['image']) ?>" width="200" height="130" /></td>
                    <td>
                        <h4><?= htmlspecialchars($item['name']) ?></h4>
                        price:<?= htmlspecialchars($item['price']) ?><br /><br />
                        <form method="POST">
                            <input type="hidden" name="menu_item_id" value="<?= htmlspecialchars($item['id']) ?>">
                            <button type="submit" name="add_to_cart" class="add-btn">Add</button>
                        </form>
                        <br /><br />
                    </td>
                <?php
                    $item_count++;
                }
                ?>
            </tr>
        </table>
        <br /><br />
    <?php } ?>

    <footer>
        <center>
            <p>&copy; 2025 YourWebsite. All rights reserved.</p>
        </center>
    </footer>

</body>
</html>
<?php $conn->close(); ?>