<?php
// Database connection details
$DBServerName = "localhost";
$DBUserName   = "root"; 
$DBPassword   = "";     
$DBName       = "foodorder_db"; 

$conn = mysqli_connect($DBServerName, $DBUserName, $DBPassword, $DBName);
if (!$conn) die("Connection failed: " . mysqli_connect_error());

// --- C. CREATE (Add a new menu item) ---
if (isset($_POST['add_item'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $image = mysqli_real_escape_string($conn, $_POST['image']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    
    $sql = "INSERT INTO menu_items (name, price, image, category) VALUES ('$name', '$price', '$image', '$category')";
    
    if ($conn->query($sql)) {
        echo "<p class='msg success'>✅ Item '$name' added successfully.</p>";
    } else {
        echo "<p class='msg error'>❌ Error adding item: " . $conn->error . "</p>";
    }
}

// --- U. UPDATE (Modify an existing menu item) ---
if (isset($_POST['update_item'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $image = mysqli_real_escape_string($conn, $_POST['image']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    
    $sql = "UPDATE menu_items SET name='$name', price='$price', image='$image', category='$category' WHERE id='$id' LIMIT 1";

    if ($conn->query($sql)) {
        echo "<p class='msg success'>✅ Item '$name' updated successfully.</p>";
    } else {
        echo "<p class='msg error'>❌ Error updating item: " . $conn->error . "</p>";
    }
}

// --- D. DELETE (Remove a menu item) ---
if (isset($_POST['delete_item'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $sql = "DELETE FROM menu_items WHERE id='$id' LIMIT 1";
    
    if ($conn->query($sql)) {
        echo "<p class='msg success'>✅ Item with ID '$id' deleted successfully.</p>";
    } else {
        echo "<p class='msg error'>❌ Error deleting item: " . $conn->error . "</p>";
    }
}

// --- R. READ (Fetch all menu items to display) ---
$sql = "SELECT id, name, price, image, category FROM menu_items ORDER BY category, id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Manage Menu</title>
    <style>
        body { font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; background: #f7f9fc; margin: 0; padding: 20px; }
        .container { max-width: 1200px; margin: auto; }
        h2 { color: #1d33f7; text-align: center; }
        .msg { width: 90%; margin: 10px auto; padding: 10px; border-radius: 5px; text-align: center; font-weight: bold; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        .add-form { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .add-form h3 { margin-top: 0; color: #1d33f7; }
        .add-form-group { margin-bottom: 15px; }
        .add-form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .add-form input, .add-form select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .add-form button { background: #28a745; color: white; border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer; }
        
        table { border-collapse: collapse; width: 100%; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-top: 20px; }
        th, td { border: 1px solid #eee; padding: 10px; text-align: left; }
        th { background: #1d33f7; color: white; text-transform: uppercase; font-size: 14px; }
        tr:nth-child(even) { background: #f9f9f9; }
        tr:hover { background: #f1f4ff; }
        
        .action-cell input, .action-cell select { width: 95%; padding: 5px; border: 1px solid #ddd; border-radius: 4px; }
        .action-cell button { padding: 5px 10px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; margin-top: 5px; }
        .update-btn { background: #1d33f7; color: white; }
        .delete-btn { background: #dc3545; color: white; }
    </style>
</head>
<body>
<div class="container">

<h2>Admin Panel - Manage Menu Items</h2>

<div class="add-form">
    <h3>Add New Menu Item</h3>
    <form method="POST">
        <div class="add-form-group">
            <label for="name">Item Name:</label>
            <input type="text" name="name" required>
        </div>
        <div class="add-form-group">
            <label for="price">Price:</label>
            <input type="number" step="0.01" name="price" required>
        </div>
        <div class="add-form-group">
            <label for="image">Image URL:</label>
            <input type="text" name="image" required>
        </div>
        <div class="add-form-group">
            <label for="category">Category:</label>
            <select name="category" required>
                <option value="Pizza">Pizza</option>
                <option value="Burger">Burger</option>
                <option value="Sizzler">Sizzler</option>
                <option value="Fries">Fries</option>
                <option value="Nachos">Nachos</option>
                <option value="Beverages">Beverages</option>
                <option value="Coffee">Coffee</option>
                <option value="Dessert">Dessert</option>
            </select>
        </div>
        <button type="submit" name="add_item">Add Item</button>
    </form>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Price</th>
            <th>Image URL</th>
            <th>Category</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <form method="POST">
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td class="action-cell"><input type="text" name="name" value="<?= htmlspecialchars($row['name']) ?>"></td>
                <td class="action-cell"><input type="number" step="0.01" name="price" value="<?= htmlspecialchars($row['price']) ?>"></td>
                <td class="action-cell"><input type="text" name="image" value="<?= htmlspecialchars($row['image']) ?>"></td>
                <td class="action-cell">
                    <select name="category" required>
                        <option value="Pizza" <?= $row['category'] == 'Pizza' ? 'selected' : '' ?>>Pizza</option>
                        <option value="Burger" <?= $row['category'] == 'Burger' ? 'selected' : '' ?>>Burger</option>
                        <option value="Sizzler" <?= $row['category'] == 'Sizzler' ? 'selected' : '' ?>>Sizzler</option>
                        <option value="Fries" <?= $row['category'] == 'Fries' ? 'selected' : '' ?>>Fries</option>
                        <option value="Nachos" <?= $row['category'] == 'Nachos' ? 'selected' : '' ?>>Nachos</option>
                        <option value="Beverages" <?= $row['category'] == 'Beverages' ? 'selected' : '' ?>>Beverages</option>
                        <option value="Coffee" <?= $row['category'] == 'Coffee' ? 'selected' : '' ?>>Coffee</option>
                        <option value="Dessert" <?= $row['category'] == 'Dessert' ? 'selected' : '' ?>>Dessert</option>
                    </select>
                </td>
                <td class="action-cell">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
                    <button type="submit" name="update_item" class="update-btn">Update</button>
                    <button type="submit" name="delete_item" class="delete-btn" onclick="return confirm('Delete this item?')">Delete</button>
                </td>
            </form>
        </tr>
        <?php } ?>
    </tbody>
</table>

</div>
</body>
</html>
<?php $conn->close(); ?>