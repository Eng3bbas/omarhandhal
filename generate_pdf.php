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

// ✅ جلب بيانات المستخدم
$get_user_info = "SELECT username, user_age, user_length, user_weight, user_status, user_child, profileimage, gender, activity_level FROM members WHERE user_id = $userId";
$user_result = pg_query($conn, $get_user_info);
if (!$user_result || pg_num_rows($user_result) === 0) {
    die("لا توجد معلومات شخصية لهذا المتدرب.");
}
$user_data = pg_fetch_assoc($user_result);

// ✅ ترجمة القيم
$genderLabel = ($user_data['gender'] === 'M') ? 'ذكر' : 'أنثى';

switch ($user_data['activity_level']) {
    case '1': $activityLabel = 'قليل'; $factor = 1.2; break;
    case '2': $activityLabel = 'متوسط'; $factor = 1.375; break;
    case '3': $activityLabel = 'مرتفع'; $factor = 1.55; break;
    case '4': $activityLabel = 'نشاط جدا مرتفع'; $factor = 1.725; break;
    default:  $activityLabel = 'محترف'; $factor = 1.9;
}

// ✅ حساب السعرات الحرارية اليومية (TDEE)
$weight = (float)$user_data['user_weight'];
$height = (float)$user_data['user_length'];
$age = (int)$user_data['user_age'];
$gender = $user_data['gender'];

if ($gender === 'M') {
    $bmr = (10 * $weight) + (6.25 * $height) - (5 * $age) + 5;
} elseif ($gender === 'F') {
    $bmr = (10 * $weight) + (6.25 * $height) - (5 * $age) - 161;
} else {
    $bmr = 0;
}

$calories = round($bmr * $factor);

// ✅ تحقق من الطلب
$check_order = "SELECT 1 FROM course_orders WHERE order_id = $orderId AND user_id = $userId AND status = 'done'";
$check_result = pg_query($conn, $check_order);
if (!$check_result || pg_num_rows($check_result) === 0) {
    die("لا تملك صلاحية الوصول لهذا الطلب أو الطلب غير مكتمل.");
}

// ✅ جلب الوجبات
$get_meals = "SELECT meal_name, category_name, meal_number, weight, weight_unit FROM user_meals WHERE order_id = $orderId ORDER BY meal_number";
$meal_result = pg_query($conn, $get_meals);
if (!$meal_result || pg_num_rows($meal_result) === 0) {
    die("لا توجد وجبات لهذا الطلب.");
}

// ✅ إعداد PDF
$mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4', 'default_font' => 'Amiri']);

function convertNumberToWord($num) {
    $words = [
        1 => 'الوجبة الأولى',
        2 => 'الوجبة الثانية',
        3 => 'الوجبة الثالثة',
        4 => 'الوجبة الرابعة',
        5 => 'الوجبة الخامسة',
        6 => 'الوجبة السادسة',
        7 => 'الوجبة السابعة',
        8 => 'الوجبة الثامنة',
        9 => 'الوجبة التاسعة',
        10 => 'الوجبة العاشرة',
        11 => 'الوجبة الحادية عشر',
        12 => 'الوجبة الثانية عشر',
        13 => 'الوجبة الثالثة عشر',
        14 => 'الوجبة الرابعة عشر',
        15 => 'الوجبة الخامسة عشر',
        16 => 'الوجبة السادسة عشر',
    ];
    return $words[$num] ?? "الوجبة رقم $num";
}

// ✅ بناء الـ HTML
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
        }
        .info-table td {
            color: white;
            font-size: 16px;
            padding: 15px;
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
        .category-label {
            color: #003366;
            font-weight: bold;
            font-size: 18px;
        }
    </style>
</head>
<body>";

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

// ✅ جدول الوجبات
$html .= "<h2>كورس التغذية رقم $orderId</h2>";
$html .= "<table class='main-table'>
    <thead>
        <tr>
            <th>الوجبة</th>
            <th>تفاصيل الوجبات</th>
        </tr>
    </thead>
    <tbody>";

$mealData = [];
while ($row = pg_fetch_assoc($meal_result)) {
    $mealNumber = (int) $row['meal_number'];
    $mealText = convertNumberToWord($mealNumber);
    $category = htmlspecialchars($row['category_name']);
    $meal = htmlspecialchars($row['meal_name']);
    $weight = htmlspecialchars($row['weight']);
    $unit = htmlspecialchars($row['weight_unit']);
    $weightDisplay = ($unit === 'عدد') ? "$weight عدد" : "$weight غم";

    if (!isset($mealData[$mealNumber])) {
        $mealData[$mealNumber] = [
            'mealText' => $mealText,
            'meals' => []
        ];
    }
    $mealData[$mealNumber]['meals'][] = ["category" => $category, "meal" => "$meal: $weightDisplay"];
}

foreach ($mealData as $mealInfo) {
    $categories = [];
    foreach ($mealInfo['meals'] as $mealDetails) {
        $categories[$mealDetails['category']][] = $mealDetails['meal'];
    }
    $mealLine = [];
    foreach ($categories as $category => $meals) {
        $mealLine[] = "<span class='category-label'>" . $category . ":</span> " . (count($meals) > 1 ? 'أما ' : '') . implode(" أو ", $meals);
    }

    $html .= "<tr>
        <td>{$mealInfo['mealText']}</td>
        <td>" . implode("<br>", $mealLine) . "</td>
    </tr>";
}

$html .= "</tbody></table></body></html>";

// ✅ إخراج PDF
$mpdf->WriteHTML($html);
$mpdf->Output();
