<?php
require 'config.php';
require 'vendor/autoload.php';

use Mpdf\Mpdf;

session_start();

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('الرجاء تسجيل الدخول أولاً.'); window.location.href = 'login.php';</script>";
    exit;
}

if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    die("رقم الطلب غير صالح.");
}

$orderId = (int)$_GET['order_id'];
$userId = (int)$_SESSION['user_id'];

// ✅ جلب معلومات المستخدم
$get_user_info = "SELECT username, user_age, user_length, user_weight, user_status, user_child, gender, activity_level FROM members WHERE user_id = $userId";
$user_result = pg_query($conn, $get_user_info);
if (!$user_result || pg_num_rows($user_result) === 0) {
    die("لا توجد معلومات شخصية لهذا المتدرب.");
}
$user_data = pg_fetch_assoc($user_result);

// ✅ ترجمة القيم
$genderLabel = ($user_data['gender'] === 'M') ? 'ذكر' : 'أنثى';

switch ($user_data['activity_level']) {
    case '1': $activityLabel = 'قليل'; break;
    case '2': $activityLabel = 'متوسط'; break;
    case '3': $activityLabel = 'مرتفع'; break;
    case '4': $activityLabel = 'نشاط جدا مرتفع'; break;
    default:  $activityLabel = 'محترف'; break;
}

// ✅ حساب السعرات الحرارية اليومية
$weight = (float)$user_data['user_weight'];
$height = (float)$user_data['user_length'];
$age = (int)$user_data['user_age'];
$gender = $user_data['gender'];
$activity_level = (int)$user_data['activity_level'];

if ($gender === 'M') {
    $bmr = (10 * $weight) + (6.25 * $height) - (5 * $age) + 5;
} elseif ($gender === 'F') {
    $bmr = (10 * $weight) + (6.25 * $height) - (5 * $age) - 161;
} else {
    $bmr = 0;
}

switch ($activity_level) {
    case 1: $factor = 1.2; break;
    case 2: $factor = 1.375; break;
    case 3: $factor = 1.55; break;
    case 4: $factor = 1.725; break;
    case 5: $factor = 1.9; break;
    default: $factor = 1.2;
}

$calories = round($bmr * $factor);

// ✅ تحقق من الطلب
$check_order = "SELECT 1 FROM course_orders WHERE order_id = $orderId AND user_id = $userId AND status = 'done'";
$check_result = pg_query($conn, $check_order);
if (!$check_result || pg_num_rows($check_result) === 0) {
    die("لا تملك صلاحية الوصول لهذا الطلب أو الطلب غير مكتمل.");
}

// ✅ تحديد رقم كورس التمرين
$get_all_orders = "SELECT order_id FROM course_orders WHERE user_id = $userId AND status = 'done' AND course_type = 'تمارين' ORDER BY order_id";
$all_orders_result = pg_query($conn, $get_all_orders);
$order_number = 0;
$index = 1;
while ($row = pg_fetch_assoc($all_orders_result)) {
    if ((int)$row['order_id'] === $orderId) {
        $order_number = $index;
        break;
    }
    $index++;
}

// ✅ جلب التمارين
$get_exercises = "SELECT exercise_name, muscle_group_name, sets FROM user_exercises WHERE order_id = $orderId";
$ex_result = pg_query($conn, $get_exercises);
if (!$ex_result || pg_num_rows($ex_result) === 0) {
    die("لا توجد تمارين لهذا الطلب.");
}

// ✅ إعداد PDF
$mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4', 'default_font' => 'Amiri']);

// ✅ HTML
$html = "<html dir='rtl'>
<head>
    <style>
        @page {
            background-image: url('2.jpg');
            background-image-resize: 6;
        }
        body {
            font-family: Amiri, sans-serif;
            text-align: center;
        }
        h2 {
            color: #007bff;
            font-size: 22px;
            line-height: 1.5;
        }
        .info-title {
            color: white;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .info-table {
            width: 90%;
            margin: 0 auto 30px auto;
            border-collapse: collapse;
            background-color: rgba(255, 255, 255, 0.1);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        .info-table th {
            background-color: rgba(0, 123, 255, 0.1);
            color: #fff;
            font-size: 16px;
            padding: 15px;
            text-align: center;
        }
        .info-table td {
            color: white;
            font-size: 16px;
            padding: 15px;
            text-align: center;
        }
        table.main-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        table.main-table th, table.main-table td {
            padding: 20px;
            text-align: center;
            font-size: 18px;
        }
        table.main-table th {
            background-color: rgba(0, 123, 255, 0.1);
            color: white;
        }
        table.main-table td {
            color: white;
            font-size: 16px;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 8px;
        }
    </style>
</head>
<body>";

// ✅ معلومات المستخدم
$html .= "<h2 class='info-title'>معلومات المتدرب: <span style='color: #000080;'>". htmlspecialchars($user_data['username']) . "</span></h2>";

$html .= "<table class='info-table'>
    <thead>
        <tr>
            <th>الاسم</th>
            <th>العمر</th>
            <th>الطول (سم)</th>
            <th>الوزن (كغم)</th>
            <th>الحالة الاجتماعية</th>
            <th>عدد الأطفال</th>
            <th>الجنس</th>
            <th>النشاط البدني</th>
            <th>السعرات اليومية</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>" . htmlspecialchars($user_data['username']) . "</td>
            <td>" . htmlspecialchars($user_data['user_age']) . "</td>
            <td>" . htmlspecialchars($user_data['user_length']) . "</td>
            <td>" . htmlspecialchars($user_data['user_weight']) . "</td>
            <td>" . htmlspecialchars($user_data['user_status']) . "</td>
            <td>" . htmlspecialchars($user_data['user_child']) . "</td>
            <td>" . htmlspecialchars($genderLabel) . "</td>
            <td>" . htmlspecialchars($activityLabel) . "</td>
            <td>" . $calories . " سعرة</td>
        </tr>
    </tbody>
</table>";

// ✅ جدول التمارين بالتنسيق الجديد
$html .= "<h2>كورس التمارين رقم $order_number</h2>";
$html .= "<table class='main-table'>
    <thead>
        <tr>
            <th>المجموعة العضلية</th>
            <th>اسم التمرين</th>
            <th>عدد المجاميع</th>
        </tr>
    </thead>
    <tbody>";

// تجميع التمارين حسب المجموعة العضلية
$grouped_exercises = [];
pg_result_seek($ex_result, 0); // إعادة المؤشر للبداية
while ($row = pg_fetch_assoc($ex_result)) {
    $group = $row['muscle_group_name'];
    $grouped_exercises[$group][] = [
        'name' => $row['exercise_name'],
        'sets' => $row['sets']
    ];
}

// توليد الصفوف مع rowspan
foreach ($grouped_exercises as $group => $exercises) {
    $first = true;
    foreach ($exercises as $ex) {
        $html .= "<tr>";
        if ($first) {
            $html .= "<td rowspan='" . count($exercises) . "'>" . htmlspecialchars($group) . "</td>";
            $first = false;
        }
        $html .= "<td>" . htmlspecialchars($ex['name']) . "</td>";
        $html .= "<td>" . htmlspecialchars($ex['sets']) . "</td>";
        $html .= "</tr>";
    }
}

$html .= "</tbody></table></body></html>";

// ✅ إخراج PDF
$mpdf->WriteHTML($html);
$mpdf->Output("exercise_course_$order_number.pdf", 'I');
?>
