<?php
require 'config.php';
global $conn;

session_start();

$username = $_SESSION['username'] ?? '';
$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    die('❌ يجب تسجيل الدخول لعرض السلة.');
}

// Check for pending orders
$checkPending = pg_query($conn, "SELECT 1 FROM product_orders WHERE user_id = $userId AND status = 'pending'");
$hasPendingOrder = pg_num_rows($checkPending) > 0;

// Handle order submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_now'])) {
    $cartQuery = "SELECT * FROM cart WHERE user_id = $userId";
    $cartResult = pg_query($conn, $cartQuery);

    if ($cartResult && pg_num_rows($cartResult) > 0) {
        while ($cartItem = pg_fetch_assoc($cartResult)) {
            $productId = $cartItem['product_id'];
            $quantity = $cartItem['quantity'];
            $price = $cartItem['price'];

            $insertQuery = "INSERT INTO product_orders (user_id, product_id, status, quantity, price)
                            VALUES ($userId, $productId, 'pending', $quantity, $price)";
            pg_query($conn, $insertQuery);
        }

        pg_query($conn, "DELETE FROM cart WHERE user_id = $userId");
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Fetch user's cart items with product info
$query = "SELECT * FROM cart WHERE user_id = $userId";
$result = pg_query($conn, $query);

if (!$result) {
    die('❌ هناك خطأ في استعلام السلة.');
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>سلة التسوق</title>
    <link rel="stylesheet" href="style4.css">
    <style>
        body {
            background-color: #f0f0f0;
        }

        .cart-container {
            max-width: 800px;
            margin: 50px auto;
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            direction: rtl;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 12px;
            margin-top: 20px;
            font-size: 16px;
        }

        table th, table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }

        table th {
            background-color: #f5f5f5;
            color: #444;
        }

        .total {
            text-align: left;
            font-size: 18px;
            margin-top: 20px;
            color: #000;
        }

        .order-section {
            text-align: center;
            margin-top: 30px;
        }

        .order-button {
            padding: 12px 25px;
            font-size: 18px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .phone-label {
            margin-top: 10px;
            font-size: 16px;
            color: #333;
        }

        .pending-message {
            font-size: 22px;
            color: white;
            text-align: center;
            padding: 50px 0;
        }

        .pending-container {
            background-color: #444;
        }
    </style>
</head>
<body>

<div class="cart-container <?php echo $hasPendingOrder ? 'pending-container' : ''; ?>">
    <?php if ($hasPendingOrder): ?>
        <div class="pending-message">⏳ جاري معالجة طلبك</div>
        <center>
        <a href="dashboarduser.php">رجوع الى الصفحة الشخصية</a></center>
    <?php elseif (pg_num_rows($result) > 0): ?>
        <h2>🛒 سلة التسوق</h2>
        <table>
            <tr>
                <th>المنتج</th>
                <th>السعر</th>
                <th>الكمية</th>
                <th>الإجمالي</th>
            </tr>
            <?php
            $grandTotal = 0;
            while ($row = pg_fetch_assoc($result)):
                $product_id = $row['product_id'];

                if (!$product_id) {
                    continue;
                }

                $query2 = "SELECT product_name FROM products WHERE id = $product_id";
                $result2 = pg_query($conn, $query2);

                if (!$result2) {
                    die('❌ هناك خطأ في استعلام المنتج.');
                }

                $row2 = pg_fetch_assoc($result2);
                $total = $row['price'] * $row['quantity'];
                $grandTotal += $total;
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($row2['product_name']); ?></td>
                    <td><?php echo number_format($row['price']); ?> د.ع</td>
                    <td><?php echo $row['quantity']; ?></td>
                    <td><?php echo number_format($total); ?> د.ع</td>
                </tr>
            <?php endwhile; ?>
        </table>

        <p class="total">الإجمالي الكلي: <?php echo number_format($grandTotal); ?> د.ع</p>

        <form method="post" class="order-section">
            <button type="submit" name="order_now" class="order-button">🛍️ ادفع الان الى الحساب الموجود تحت</button>
            <p class="phone-label"> hgkd2345</p><br>
            <p class="phone-label">   ثم ابعث مسج  عبر الواتس يتضمن صورة (بالمواد + قيمة تحويل المبلغ) للرقم الظاهر امامك ليتم تجهيز طلبك</p>
            <p class="phone-label">📞 07706928402 </p>
            <a class="phone-label" href="dashboarduser.php">الرجوع الى الصفحة الرئيسية</a>

        </form>

    <?php else: ?>
        <p>سلتك فارغة حالياً.</p>
    <?php endif; ?>
</div>

</body>
</html>
