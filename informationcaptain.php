<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require 'config.php';
global $conn;

// استقبال البيانات
$Post01 = $_POST['Get01'] ?? '';
$Post02 = $_POST['Get02'] ?? '';
$Post03 = $_POST['Get03'] ?? '';
$Post04 = $_POST['Get04'] ?? '';
$Post05 = $_POST['Get05'] ?? '';
$Post06 = $_POST['Get06'] ?? '';
$Post07 = $_POST['Get07'] ?? '';
$Post08 = $_POST['Get08'] ?? '';

$Error = '';

// تحويل الجنس إلى رمز
$genderCode = $Post07 === "ذكر" ? "M" : ($Post07 === "أنثى" ? "F" : null);

// تحويل مستوى النشاط إلى رقم
$activityLevel = match($Post08) {
    'قليل' => 1,
    'متوسط' => 2,
    'مرتفع' => 3,
    'نشط جدا' => 4,
    'محترف' => 5,
    default => null,
};

// معالجة الصورة
$base64Image = null;
if (!empty($_FILES['profileimage']['tmp_name']) && $_FILES['profileimage']['error'] === UPLOAD_ERR_OK) {
    $imageData = file_get_contents($_FILES['profileimage']['tmp_name']);
    $base64Image = base64_encode($imageData);
}

// إنشاء رمز فريد
$Token = date('ymdhis') . rand(10, 99);

if (isset($_POST['information'])) {

    // التحقق من القيم النصية
    if (
        trim($Post01) === '' || trim($Post02) === '' || trim($Post03) === '' ||
        trim($Post04) === '' || trim($Post05) === '' || trim($Post06) === '' ||
        trim($Post07) === '' || trim($Post08) === ''
    ) {
        $Error = '<p class="error">⚠️ الرجاء ملء جميع الحقول</p>';
    } elseif (!isset($_FILES['profileimage']) || $_FILES['profileimage']['error'] !== UPLOAD_ERR_OK) {
        $Error = '<p class="error">⚠️ الرجاء رفع صورة شخصية</p>';
    } else {
        // فحص اسم المستخدم في قاعدة البيانات
        $userResult = pg_query_params($conn, "SELECT id, username FROM users WHERE username = $1", [$Post01]);
        if (!$userResult || pg_num_rows($userResult) === 0) {
            $Error = '<p class="error">❌ المستخدم غير موجود.</p>';
        } else {
            $userData = pg_fetch_assoc($userResult);
            $userId = $userData['id'];
            $username = $userData['username'];

            // التحقق من العضو
            $checkResult = pg_query_params($conn, "SELECT * FROM members WHERE user_id = $1", [$userId]);
            if (pg_num_rows($checkResult) > 0) {
                $_SESSION['username'] = $username;
                $_SESSION['base64Image'] = $base64Image;
                header("Location: dashboarduser.php");
                exit();
            }

            // إدخال بيانات العضو
            $insert = pg_query_params($conn, "INSERT INTO members (
                token, username, user_age, user_length, user_weight,
                user_status, user_child, profileImage, gender, activity_level, user_id
            ) VALUES (
                $1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11
            )", [$Token, $username, $Post02, $Post03, $Post04, $Post05, $Post06, $base64Image, $genderCode, $activityLevel, $userId]);

            if ($insert) {
                $_SESSION['username'] = $username;
                $_SESSION['base64Image'] = $base64Image;
                echo '
                <style>
                    body { text-align: center; font-family: "Poppins"; background: #1b1b1b; color: #45ffca; padding-top: 40px; }
                    img { max-width: 200px; border-radius: 10px; margin-bottom: 20px; }
                    .msg { font-size: 18px; }
                </style>
                <div>
                    <img src="data:image/png;base64,' . $base64Image . '" alt="profile" />
                    <p class="msg">✅ تم الحفظ بنجاح. جارٍ التحويل...</p>
                </div>
                <meta http-equiv="refresh" content="3;url=dashboarduser.php">
                ';
                exit();
            } else {
                $Error = '<p class="error">⚠️ فشل الإدخال: ' . pg_last_error($conn) . '</p>';
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>نموذج التسجيل</title>
    <link rel="stylesheet" href="style5.css">
</head>
<body>

<?= $Error ?>

<div class="box">
    <form method="POST" enctype="multipart/form-data">
        <h2>📋 بيانات المستخدم</h2>

        <div class="inputbox">
            <input type="text" name="Get01" placeholder="اسم المستخدم" value="<?= htmlspecialchars($Post01) ?>" required>
        </div>

        <div class="file">
            <input type="file" name="profileimage" id="profileimage" accept="image/*" required onchange="previewImage(event)">
            <label for="profileimage">📷 اختر صورة</label>
            <img id="preview" src="#" style="display:none;" />
        </div>

        <div class="inputbox"><input type="number" name="Get02" placeholder="العمر" value="<?= htmlspecialchars($Post02) ?>" required></div>
        <div class="inputbox"><input type="number" name="Get03" placeholder="الطول (سم)" value="<?= htmlspecialchars($Post03) ?>" required></div>
        <div class="inputbox"><input type="number" name="Get04" placeholder="الوزن (كجم)" value="<?= htmlspecialchars($Post04) ?>" required></div>

        <div class="inputbox">
            <select name="Get05" required>
                <option value="" disabled <?= $Post05 == '' ? 'selected' : '' ?>>اختر الحالة الاجتماعية</option>
                <option value="أعزب" <?= $Post05 == 'أعزب' ? 'selected' : '' ?>>أعزب</option>
                <option value="متزوج" <?= $Post05 == 'متزوج' ? 'selected' : '' ?>>متزوج</option>
                <option value="مطلق" <?= $Post05 == 'مطلق' ? 'selected' : '' ?>>مطلق</option>
                <option value="أرمل" <?= $Post05 == 'أرمل' ? 'selected' : '' ?>>أرمل</option>
            </select>
        </div>

        <div class="inputbox">
            <select name="Get06" required>
                <option value="" disabled <?= $Post06 == '' ? 'selected' : '' ?>>عدد الأطفال</option>
                <option value="0" <?= $Post06 == '0' ? 'selected' : '' ?>>0</option>
                <option value="1" <?= $Post06 == '1' ? 'selected' : '' ?>>1</option>
                <option value="2" <?= $Post06 == '2' ? 'selected' : '' ?>>2</option>
                <option value="3" <?= $Post06 == '3' ? 'selected' : '' ?>>3</option>
                <option value="4" <?= $Post06 == '4' ? 'selected' : '' ?>>4 أو أكثر</option>
            </select>
        </div>

        <div class="inputbox">
            <select name="Get07" required>
                <option value="" disabled <?= $Post07 == '' ? 'selected' : '' ?>>الجنس</option>
                <option value="ذكر" <?= $Post07 == 'ذكر' ? 'selected' : '' ?>>ذكر</option>
                <option value="أنثى" <?= $Post07 == 'أنثى' ? 'selected' : '' ?>>أنثى</option>
            </select>
        </div>

        <div class="inputbox">
            <select name="Get08" required>
                <option value="" disabled <?= $Post08 == '' ? 'selected' : '' ?>>مستوى النشاط البدني</option>
                <option value="قليل" <?= $Post08 == 'قليل' ? 'selected' : '' ?>>قليل</option>
                <option value="متوسط" <?= $Post08 == 'متوسط' ? 'selected' : '' ?>>متوسط</option>
                <option value="مرتفع" <?= $Post08 == 'مرتفع' ? 'selected' : '' ?>>مرتفع</option>
                <option value="نشط جدا" <?= $Post08 == 'نشط جدا' ? 'selected' : '' ?>>نشط جداً</option>
                <option value="محترف" <?= $Post08 == 'محترف' ? 'selected' : '' ?>>محترف</option>
            </select>
        </div>

        <input type="submit" name="information" value="💾 حفظ البيانات">
    </form>
</div>

<script>
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = () => {
        const img = document.getElementById('preview');
        img.src = reader.result;
        img.style.display = 'block';
    };
    reader.readAsDataURL(event.target.files[0]);
}
</script>

</body>
</html>
