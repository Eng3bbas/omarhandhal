<?php
require 'config.php';
global $conn;

if (!isset($_GET['user_id']) || !is_numeric($_GET['user_id'])) {
    echo "<script>alert('رقم المستخدم غير صحيح.'); window.location.href = 'display_members.php';</script>";
    exit;
}

$userId = (int)$_GET['user_id'];

// جلب الطلبات المكتملة لهذا العضو
$get_all_done_orders = "SELECT order_id FROM course_orders WHERE user_id = $userId AND status = 'done'";
$orders_result = pg_query($conn, $get_all_done_orders);

$orders = [];
if ($orders_result && pg_num_rows($orders_result) > 0) {
    while ($row = pg_fetch_assoc($orders_result)) {
        $orders[] = $row['order_id'];
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>الكورسات الخاصة بالعضو</title>
    <style>
        body {
            background-color: #f4f4f9;
            font-family: 'Arial', sans-serif;
            color: #333;
            padding: 20px;
        }
        h2 {
            text-align: center;
            color: #2c3e50;
        }
        .course-box {
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #3498db;
            color: white;
        }
        td {
            background-color: #f9f9f9;
        }
        .pdf-button {
            margin-top: 10px;
            text-align: center;
        }
        .pdf-button button {
            background-color: #2ecc71;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }
        .no-data {
            padding: 20px;
            background-color: #e0e0e0;
            border-radius: 8px;
            text-align: center;
            font-size: 18px;
            color: #555;
        }
    </style>
</head>
<body>

<h2>الكورسات الخاصة بالعضو رقم <?= $userId ?></h2>

<?php if (count($orders) > 0): ?>

    <!-- تمارين -->
    <div class="course-box">
        <h3>التمارين</h3>
        <?php
        $index = 1;
        foreach ($orders as $orderId):
            $get_exercises = "SELECT exercise_name, muscle_group_name, sets FROM user_exercises WHERE order_id = $orderId";
            $exercise_result = pg_query($conn, $get_exercises);

            if ($exercise_result && pg_num_rows($exercise_result) > 0):
                echo "<h4>كورس تمرين رقم $index</h4>";
                echo "<table>
                        <thead><tr><th>المجموعة العضلية</th><th>التمرين</th><th>عدد المجاميع</th></tr></thead><tbody>";
                while ($ex = pg_fetch_assoc($exercise_result)) {
                    echo "<tr>
                            <td>" . htmlspecialchars($ex['muscle_group_name']) . "</td>
                            <td>" . htmlspecialchars($ex['exercise_name']) . "</td>
                            <td>" . htmlspecialchars($ex['sets']) . "</td>
                          </tr>";
                }
                echo "</tbody></table>";

                // زر تحميل PDF للتمارين
                echo "<div class='pdf-button'>
                        <form action='exercise_pdf.php' method='get' target='_blank'>
                            <input type='hidden' name='order_id' value='$orderId'>
                            <button>📄 تحميل كورس تمرين رقم $index كـ PDF</button>
                        </form>
                      </div>";
                $index++;
            endif;
        endforeach;
        ?>
    </div>

    <!-- الوجبات -->
    <div class="course-box">
        <h3>الوجبات</h3>
        <?php
        $index = 1;
        foreach ($orders as $orderId):
            $get_meals = "SELECT meal_name, category_name, meal_number, weight, weight_unit FROM user_meals WHERE order_id = $orderId ORDER BY meal_number";
            $meal_result = pg_query($conn, $get_meals);

            if ($meal_result && pg_num_rows($meal_result) > 0):
                echo "<h4>كورس تغذية رقم $index</h4>";
                echo "<table>
                        <thead><tr><th>رقم الوجبة</th><th>الفئة</th><th>الوجبة</th><th>الكمية</th></tr></thead><tbody>";
                while ($meal = pg_fetch_assoc($meal_result)) {
                    $unit = htmlspecialchars($meal['weight_unit']);
                    $weight = htmlspecialchars($meal['weight']);
                    $displayWeight = ($unit === 'عدد') ? "$weight عدد" : "$weight غم";
                    echo "<tr>
                            <td>" . htmlspecialchars($meal['meal_number']) . "</td>
                            <td>" . htmlspecialchars($meal['category_name']) . "</td>
                            <td>" . htmlspecialchars($meal['meal_name']) . "</td>
                            <td>$displayWeight</td>
                          </tr>";
                }
                echo "</tbody></table>";

                // زر تحميل PDF للوجبات
                echo "<div class='pdf-button'>
                        <form action='generate_pdf.php' method='get' target='_blank'>
                            <input type='hidden' name='order_id' value='$orderId'>
                            <button>📄 تحميل كورس التغذية رقم $index كـ PDF</button>
                        </form>
                      </div>";
                $index++;
            endif;
        endforeach;
        ?>
    </div>

<?php else: ?>
    <div class="no-data">لا توجد كورسات مكتملة لهذا العضو.</div>
<?php endif; ?>

</body>
</html>
