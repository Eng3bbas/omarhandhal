<?php
require 'config.php';
global $conn;
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('الرجاء تسجيل الدخول أولاً.'); window.location.href = 'login.php';</script>";
    exit;
}

$userId = (int)$_SESSION['user_id'];

// Get all done orders for the user
$get_all_done_orders = "SELECT order_id FROM course_orders WHERE user_id = $userId AND status = 'done'";
$orders_result = pg_query($conn, $get_all_done_orders);
$orders = [];
if (pg_num_rows($orders_result) > 0) {
    while ($row = pg_fetch_assoc($orders_result)) {
        $orders[] = $row['order_id'];
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>الدورات المكتملة</title>
    <style>
        body {
            background-color: #f4f4f9;
            color: #333;
            font-family: 'Arial', sans-serif;
            padding: 20px;
            margin: 0;
        }

        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
        }

        .course-box {
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            transition: box-shadow 0.3s ease;
        }

        .course-box:hover {
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
        }

        table th, table td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #eee;
        }

        table th {
            background-color: #3498db;
            color: white;
            font-size: 16px;
            text-transform: uppercase;
        }

        table td {
            background-color: #f9f9f9;
            color: #555;
            font-size: 14px;
        }

        table td a {
            color: #3498db;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        table td a:hover {
            color: #1abc9c;
            text-decoration: underline;
        }

        .pdf-button {
            margin-top: 20px;
            text-align: center;
        }

        .pdf-button button {
            padding: 12px 25px;
            background-color: #2ecc71;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .pdf-button button:hover {
            background-color: #27ae60;
        }

        .weight-cell {
            background-color: #ecf0f1;
            font-weight: bold;
            text-align: center;
        }

        .no-data {
            padding: 40px;
            background-color: #bdc3c7;
            border-radius: 10px;
            text-align: center;
            font-size: 22px;
            color: #34495e;
        }

        /* تأثيرات للأزرار و الصور */
        .course-box h3 {
            font-size: 22px;
            color: #34495e;
            margin-bottom: 15px;
            font-weight: bold;
        }

        /* Animation for table hover */
        table tr:hover {
            background-color: #ecf0f1;
            cursor: pointer;
            transform: translateY(-3px);
            transition: transform 0.2s ease-in-out;
        }

    </style>
</head>
<body>

<h2>الدورات المكتملة حسب التمارين والوجبات</h2>

<?php if (count($orders) > 0): ?>

    <!-- Exercises Table -->
    <div class="course-box">
        <h3>تمارين</h3>
        <?php
        $colors = ['#d32f2f', '#1976d2', '#388e3c', '#f57c00', '#7b1fa2'];
        $index = 1;
        $colorIndex = 0;

        foreach ($orders as $orderId) {
            $get_exercises = "SELECT exercise_name, muscle_group_name, sets FROM user_exercises WHERE order_id = $orderId";
            $ex_result = pg_query($conn, $get_exercises);

            if ($ex_result && pg_num_rows($ex_result) > 0) {
                $color = $colors[$colorIndex % count($colors)];
                echo "<h3 style='color: $color;'>كورس تمرين رقم " . $index . "</h3>";
                echo "<table>
                        <thead>
                            <tr>
                                <th>المجموعة العضلية</th>
                                <th>اسم التمرين</th>
                                <th>عدد المجاميع</th>
                            </tr>
                        </thead>
                        <tbody>";
                while ($row = pg_fetch_assoc($ex_result)) {
                    echo "<tr>
                            <td>" . htmlspecialchars($row['muscle_group_name']) . "</td>
                            <td>" . htmlspecialchars($row['exercise_name']) . "</td>
                            <td>" . htmlspecialchars($row['sets']) . "</td>
                          </tr>";
                }
                echo "</tbody></table>";
                // Add a button to download this exercise course as a PDF
                echo "<div class='pdf-button'>
                        <form action='exercise_pdf.php' method='get' target='_blank'>
                            <input type='hidden' name='order_id' value='$orderId'>
                            <button>📄 تحميل كورس تمرين رقم $index كـ PDF</button>
                        </form>
                      </div>";
                $index++;
                $colorIndex++;
            }
        }
        ?>
    </div>

    <!-- Meals Table -->
    <div class="course-box">
        <h3>الوجبات</h3>
        <?php
        $index = 1;
        $colorIndex = 0;

        foreach ($orders as $orderId) {
            // ✅ جلب weight_unit من قاعدة البيانات
            $get_meals = "SELECT meal_name, category_name, meal_number, weight, weight_unit FROM user_meals WHERE order_id = $orderId ORDER BY meal_number";
            $meal_result = pg_query($conn, $get_meals);

            if ($meal_result && pg_num_rows($meal_result) > 0) {
                $mealColor = $colors[$colorIndex % count($colors)];
                echo "<h3 style='color: $mealColor;'>كورس التغذية رقم $index</h3>";
                echo "<table>
                        <thead>
                            <tr>
                                <th>رقم الوجبة</th>
                                <th>فئة الطعام</th>
                                <th>الوجبة</th>
                                <th>وزن الوجبة</th>
                            </tr>
                        </thead>
                        <tbody>";
                while ($row = pg_fetch_assoc($meal_result)) {
                    $mealNumber = isset($row['meal_number']) ? htmlspecialchars($row['meal_number']) : 'غير محدد';
                    $categoryName = htmlspecialchars($row['category_name']);
                    $mealName = htmlspecialchars($row['meal_name']);
                    $weight = htmlspecialchars($row['weight']);
                    $unit = isset($row['weight_unit']) ? htmlspecialchars($row['weight_unit']) : 'غم';

                    // التنسيق حسب الوحدة
                    if ($unit === 'عدد') {
                        $weightDisplay = "$weight عدد $mealName";
                    } else {
                        $weightDisplay = "$weight غم";
                    }

                    echo "<tr>
                            <td>$mealNumber</td>
                            <td>$categoryName</td>
                            <td>$mealName</td>
                            <td class='weight-cell'>$weightDisplay</td>
                          </tr>";
                }
                echo "</tbody></table>";
                // Add a button to download this meal course as a PDF
                echo "<div class='pdf-button'>
                        <form action='generate_pdf.php' method='get' target='_blank'>
                            <input type='hidden' name='order_id' value='$orderId'>
                            <button>📄 تحميل كورس التغذية رقم $index كـ PDF</button>
                        </form>
                      </div>";
                $index++;
                $colorIndex++;
            }
        }
        ?>
    </div>

<?php else: ?>
    <div class="no-data">لا توجد دورات مكتملة لعرضها.</div>
<?php endif; ?>

</body>
</html>
