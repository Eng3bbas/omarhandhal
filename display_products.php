<?php
// Include the config file for database connection
require 'config.php';
global $conn;

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $product_name = $_POST['product_name'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];







    // Query to insert product into the database
    $insert_query = "INSERT INTO products (product_name, price, quantity) VALUES ('$product_name', '$price', '$quantity')";

    // Execute the query
    $result = pg_query($conn, $insert_query);
}

// Fetch products to display
$select_query = "SELECT * FROM products";
$result = pg_query($conn, $select_query);

// Fetch all products
$products = pg_fetch_all($result);
?>

<!DOCTYPE html>
<html lang="en" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عرض المنتجات</title>
    <link rel="stylesheet" href="style4.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        input {
            color: black;
        }

        form {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 18px; /* Increased font size */
            margin-bottom: 10px;
            color: #fff3cd;
        }

        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 12px; /* Increased padding */
            font-size: 18px; /* Increased font size */
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
            margin-bottom: 10px;
        }

        button[type="submit"] {
            padding: 15px 25px;
            font-size: 18px;  /* Increased font size */
            background-color: #0056b3;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
            display: block;  /* Makes the button a block-level element */
            margin: 0 auto;  /* Centers the button horizontally */
        }

        button[type="submit"]:hover {
            background-color: #003366;
        }

    </style>
</head>
<body>
<div class="menu">
    <ul>
        <li class="profile">
            <div class="img-box">
                <img src="10.jpeg" alt="profile">
            </div>
            <h2>OmarHandhal</h2>
        </li>

        <li>
            <a href="captains.php">
                <i class="fas fa-home"></i>
                <p>Dashboard</p>
            </a>
        </li>

        <li>
            <a href="display_members.php">
                <i class="fas fa-user-group"></i>
                <p>المتدربين</p>
            </a>
        </li>

        <li>
            <a class="active" href="display_products.php">
                <i class="fas fa-table"></i>
                <p>المنتجات</p>
            </a>
        </li>

        <li>
            <a href="display_course_orders.php">
                <i class="fas fa-table"></i>
                <p>طلبات المتدربين</p>
            </a>
        </li>

        <li class="log-out">
            <a href="logout.php">
                <i class="fas fa-sign-out"></i>
                <p>تسجيل خروج</p>
            </a>
        </li>
    </ul>
</div>

<div class="content">
    <div class="title-info">
        <p>إضافة منتج جديد</p>
        <i class="fas fa-plus-circle"></i>
    </div>

    <!-- Product Adding Form -->
    <form action="display_products.php" method="POST">
        <div class="form-group">
            <label for="product_name">اسم المنتج</label>
            <input type="text" id="product_name" name="product_name" required>
        </div>

        <div class="form-group">
            <label for="price">السعر (IQD)</label>
            <input type="number" id="price" name="price" required>
        </div>

        <div class="form-group">
            <label for="quantity">الكمية</label>
            <input type="number" id="quantity" name="quantity" required>
        </div>

        <button type="submit" class="btn">إضافة المنتج</button>
    </form>

    <div class="title-info">
        <p>قائمة المنتجات</p>
        <i class="fas fa-table"></i>
    </div>

    <!-- Display All Products -->
    <table>
        <thead>
        <tr>
            <th>اسم المنتج</th>
            <th>السعر</th>
            <th>الكمية</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (count($products) > 0) {
            foreach ($products as $product) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($product['product_name']) . "</td>";
                echo "<td>" . htmlspecialchars($product['price']) . "</td>";
                echo "<td>" . htmlspecialchars($product['quantity']) . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='3' style='text-align: center'>لا توجد منتجات حالياً</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>
</body>
</html>
