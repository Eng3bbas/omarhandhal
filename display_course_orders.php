<?php 
require 'config.php';
global $conn;

// استعلام بـ LEFT JOIN لجلب الطلبات المعلقة مع بيانات الأعضاء (إن وجدت)
$query = "
    SELECT co.*, m.username, m.user_age, m.user_length, m.user_weight, m.user_status,
           m.user_child, m.gender, m.activity_level
    FROM course_orders co
    LEFT JOIN members m ON co.user_id = m.user_id
    WHERE co.status = 'pending'
";

$result = pg_query($conn, $query);

// معالجة النتيجة وتفادي الأخطاء
$courseOrders = ($result && pg_num_rows($result) > 0) ? pg_fetch_all($result) : [];
?>


<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>عرض الطلبات المعلقة</title>
    <link rel="stylesheet" href="style4.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="menu">
    <ul>
        <li class="profile">
            <div class="img-box"><img src="10.jpeg" alt="profile"></div>
            <h2>OmarHandhal</h2>
        </li>
        <li><a href="captains.php"><i class="fas fa-home"></i><p>Dashboard</p></a></li>
        <li><a href="display_members.php"><i class="fas fa-user-group"></i><p>المتدربين</p></a></li>
        <li><a href="display_products.php"><i class="fas fa-table"></i><p>المنتجات</p></a></li>
        <li><a class="active" href="display_course_orders.php"><i class="fas fa-table"></i><p>طلبات المتدربين</p></a></li>
        <li class="log-out"><a href="logout.php"><i class="fas fa-sign-out"></i><p>تسجيل خروج</p></a></li>
    </ul>
</div>

<div class="content">
    <div class="title-info">
        <p>الطلبات المعلقة</p>
        <i class="fas fa-table"></i>
    </div>

    <div class="data-info">
        <div class="box">
            <i class="fas fa-table"></i>
            <div class="data">
                <p>العدد الكلي</p>
                <span><?php echo count($courseOrders); ?></span>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="title-info">
            <p>الطلبات المعلقة</p>
            <i class="fas fa-table"></i>
        </div>

        <table>
            <thead>
                <tr>
                    <th>رقم الطلب</th>
                    <th>اسم الكورس</th>
                    <th>التاريخ</th>
                    <th>اسم الشخص</th>
                    <th>العمر</th>
                    <th>الطول</th>
                    <th>الوزن</th>
                    <th>الحالة الاجتماعية</th>
                    <th>عدد الأولاد</th>
                    <th>الجنس</th>
                    <th>النشاط البدني</th>
                    <th>الحالة</th>
                </tr>
            </thead>
            <tbody>
            <?php
            if (count($courseOrders) > 0) {
                foreach ($courseOrders as $order) {
                    $orderId = htmlspecialchars($order['order_id']);
                    $courseType = htmlspecialchars($order['course_type']);
                    $orderDate = htmlspecialchars($order['order_date']);
                    $status = htmlspecialchars($order['status']);
                    $user_id2 = $order['user_id'];

                    // استعلام للحصول على بيانات العضو
                    $query = "SELECT * FROM members WHERE user_id = " . pg_escape_string($conn, $user_id2);
                    $result2 = pg_query($conn, $query);

                    if ($result2 && pg_num_rows($result2) > 0) {
                        $row2 = pg_fetch_assoc($result2);

                        $username = htmlspecialchars($row2['username']);
                        $user_age = htmlspecialchars($row2['user_age']);
                        $user_length = htmlspecialchars($row2['user_length']);
                        $user_weight = htmlspecialchars($row2['user_weight']);
                        $user_status = htmlspecialchars($row2['user_status']);
                        $user_child = htmlspecialchars($row2['user_child']);

                        $activity_level_code = $row2['activity_level'] ?? null;
                        $gender_code = $row2['gender'] ?? null;

                        // ترجمة النشاط البدني
                        $activity_level_code = isset($row2['activity_level']) ? (int)$row2['activity_level'] : null;
$gender_code = isset($row2['gender']) ? $row2['gender'] : null;
                        $activity_text = match($activity_level_code) {
                            1 => 'قليل',
                            2 => 'متوسط',
                            3 => 'مرتفع',
                            4 => 'نشط جدا',
                            5 => 'محترف',
                            default => 'غير معرف',
                        };

                        // ترجمة الجنس
                        $gender_text = match($gender_code) {
                            'M' => 'ذكر',
                            'F' => 'أنثى',
                            default => 'غير معرف',
                        };
                    } else {
                        // في حال لم يتم العثور على بيانات العضو
                        $username = $user_age = $user_length = $user_weight = $user_status = $user_child = "غير متوفر";
                        $gender_text = $activity_text = "غير معرف";
                    }

                    // طباعة الصف في الجدول
                    echo "<tr>";
                    echo "<td><a href='" . ($courseType === "غذائي" ? "meal.php" : "exercises.php") . "?order_id=$orderId'>$orderId</a></td>";
                    echo "<td>$courseType</td>";
                    echo "<td>$orderDate</td>";
                    echo "<td>$username</td>";
                    echo "<td>$user_age</td>";
                    echo "<td>$user_length</td>";
                    echo "<td>$user_weight</td>";
                    echo "<td>$user_status</td>";
                    echo "<td>$user_child</td>";
                    echo "<td>$gender_text</td>";
                    echo "<td>$activity_text</td>";
                    echo "<td>$status</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='12' style='text-align: center;'>لا توجد طلبات معلقة حالياً</td></tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
