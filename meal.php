<?php
require 'config.php';
global $conn;
// Ensure $courseOrders is defined as an array
$courseOrders = isset($courseOrders) && is_array($courseOrders) ? $courseOrders : [];

// OR simply initialize it earlier like this to avoid undefined issues:
$courseOrders = []; // Default to empty array



$courseOrders = isset($courseOrders) && is_array($courseOrders) ? $courseOrders : [];
$courseOrders = []; // Default to empty array

// Then fetch course orders from the database if needed
$query = "SELECT * FROM course_orders";  // adjust this to your table name
$result = pg_query($conn, $query);
if ($result && pg_num_rows($result) > 0) {
    $courseOrders = pg_fetch_all($result);
}



$orderId = ''; // Default value
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;

if (count($courseOrders) > 0) {
    $orderId = htmlspecialchars($courseOrders[0]['order_id']); // Pick first order ID
}
// Later in the code:
if (count($courseOrders) > 0) {
    foreach ($courseOrders as $order) {
        $orderId = htmlspecialchars($order['order_id']);
    }
}
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

<br>
<br>
<div class="box">
    <i class="fa-solid fa-meat"></i>
    <div class="data">
        
        
    <?php
$orderId = $_GET['order_id']; // This should be 59
?>
  <?php
$mealLabel = "ثلاثة وجبات"; // or any logic that determines this
if ($mealLabel === "ثلاثة وجبات") {
    echo "<div style='text-align:center; margin-top:20px;'>
            <a href=\"meal_course.php?order_id=" . urlencode($orderId) . "\" style='color: blue; font-weight: bold; text-decoration: none; font-size: 22px;align_item:center;'>
                $mealLabel
            </a>
          </div>";
} else {
    echo "<a href=\"meal_course.php?order_id=" . urlencode($orderId) . "\">$mealLabel</a>";
}
?>

    </div>
</div>

<div class="box">
    <i class="fa-solid fa-meat"></i>
    <div class="data">
      
    <?php
$orderId = $_GET['order_id']; // This should be 59
?>
 
 <?php
$mealLabel = "اربعة وجبات"; // or any logic that determines this
if ($mealLabel === "اربعة وجبات") {
    echo "<div style='text-align:center; margin-top:20px;'>
            <a href=\"meal1.php?order_id=" . urlencode($orderId) . "\" style='color: blue; font-weight: bold; text-decoration: none; font-size: 22px;align_item:center;'>
                $mealLabel
            </a>
          </div>";
} else {
    echo "<a href=\"meal1.php?order_id=" . urlencode($orderId) . "\">$mealLabel</a>";
}
?>
    </div>
</div>

<div class="box">
    <i class="fa-solid fa-meat"></i>
    <div class="data">
 
             
    <?php
$orderId = $_GET['order_id']; // This should be 59?>
<?php
$mealLabel = "خمسة وجبات"; // or any logic that determines this
if ($mealLabel === "خمسة وجبات") {
    echo "<div style='text-align:center; margin-top:20px;'>
            <a href=\"meal2.php?order_id=" . urlencode($orderId) . "\" style='color: blue; font-weight: bold; text-decoration: none; font-size: 22px;align_item:center;'>
                $mealLabel
            </a>
          </div>";
} else {
    echo "<a href=\"meal2.php?order_id=" . urlencode($orderId) . "\">$mealLabel</a>";
}
?>
    </div>
</div>

<div class="box">
    <i class="fa-solid fa-meat"></i>
    <div class="data">
                       
    
            
    <?php
$orderId = $_GET['order_id']; // This should be 59?>

<?php
$mealLabel = "ستة وجبات"; // or any logic that determines this
if ($mealLabel === "ستة وجبات") {
    echo "<div style='text-align:center; margin-top:20px;'>
            <a href=\"meal4.php?order_id=" . urlencode($orderId) . "\" style='color: blue; font-weight: bold; text-decoration: none; font-size: 22px; align_item:center;'>
                $mealLabel
            </a>
          </div>";
} else {
    echo "<a href=\"meal4.php?order_id=" . urlencode($orderId) . "\">$mealLabel</a>";
}
?>
    </div>
</div>
</tbody>
        </table>
    </div>
</div>
</body>
</html>