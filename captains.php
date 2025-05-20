<?php
// Include the config file for database connection
require 'config.php';
global $conn;

// Query to get all products from the database
$select = "SELECT * FROM products";
$RunSelect = pg_query($conn, $select);

// Check if there are any products
if (pg_num_rows($RunSelect) > 0) {
    // Store the results in an array
    $products = pg_fetch_all($RunSelect);
} else {
    $products = [];
}

// Query to get all members from the database (you can adjust this for your dashboard needs)
$select_members = "SELECT * FROM members";
$RunSelectMembers = pg_query($conn, $select_members);
if (pg_num_rows($RunSelectMembers) > 0) {
    $members = pg_fetch_all($RunSelectMembers);
} else {
    $members = [];
}
?>

<!DOCTYPE html>
<html lang="en" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style4.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Dashboard</title>
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
            <a class="active" href="#">
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
            <a href="display_products.php">
                <i class="fas fa-table"></i>
                <p>المنتجات</p>
            </a>
        </li>

        <li>
            <a href="display_course_orders.php">
                <i class="fas fa-table"></i>
                <p>التمارين</p>
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
        <p>dashboard</p>
        <i class="fas fa-chart-bar"></i>
    </div>
    <div class="data-info">
        <div class="box">
            <i class="fas fa-user"></i>
            <div class="data">
                <p>المتدربين</p>
                <span><?php echo count($members); ?></span>
            </div>
        </div>


        <div class="box">
            <i class="fas fa-table"></i>
            <div class="data">
                <p>المنتجات</p>
                <span><?php echo count($products); ?></span>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="title-info">
            <p>المنتجات</p>
            <i class="fas fa-table"></i>
        </div>
        <table>
            <thead>
            <tr>
                <th>المنتج</th>
                <th>السعر</th>
                <th>الكمية</th>
            </tr>
            </thead>
            <tbody>
            <?php
            // Loop through the products and display each one in a row
            if (count($products) > 0) {
                foreach ($products as $product) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($product['product_name']) . "</td>";
                    echo "<td><span class='price'>" . htmlspecialchars($product['price']) . " IQD</span></td>";
                    echo "<td><span class='count'>" . htmlspecialchars($product['quantity']) . "</span></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3' style='text-align: center;'>لا توجد منتجات حالياً</td></tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
