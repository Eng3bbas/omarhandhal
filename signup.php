<?php
session_start();

require 'config.php';
global $conn;

@$Post01 = $_POST['Get01'];
@$Post03 = $_POST['Get03'];
@$Post04 = $_POST['Get04'];

$NewToken = date('ymdhis');
$NewTokenRand = rand(10, 100);
$Token = $NewToken . $NewTokenRand;

$SuccessMessage = ""; // متغير لرسالة الشكر
$ErrorMessage = "";  // متغير لرسالة الخطأ

// إذا كان المستخدم قد أرسل البيانات
if (isset($_POST['singup'])) {
    // التحقق من أن الحقول غير فارغة
    if (empty($Post01) || empty($Post03) || empty($Post04)) {
        $ErrorMessage = '<p class="error">لايمكنك ترك الحقول فارغة</p>';
    } else {
        // منع هجمات SQL Injection باستخدام استعلامات مهيكلة
        $select = "SELECT * FROM users WHERE username = $1";
        $result = pg_query_params($conn, $select, array($Post01));
        $role = "";
        
        // التحقق إذا كان الاسم موجود بالفعل
        if (pg_num_rows($result) > 0) {
            $ErrorMessage = '<p class="error">عذراً الاسم مستخدم بالفعل</p>';
        } else {
            // إدخال المستخدم الجديد في قاعدة البيانات
            $insert = "INSERT INTO users (token, username, phone_number, password, role)
                       VALUES ($1, $2, $3, $4, $5)";
            $params = array($Token, $Post01, $Post03, $Post04, $role);
            
            // تنفيذ الاستعلام
            $insertResult = pg_query_params($conn, $insert, $params);

            if ($insertResult) {
                // إذا تم إضافة الحساب بنجاح، عرض رسالة الشكر
                $SuccessMessage = '<p class="success">شكراً لك تم انشاء الحساب بنجاح</p>';
                
             } else {
    $ErrorMessage = '<p class="error">حدث خطأ أثناء التسجيل: ' . pg_last_error($conn) . '</p>';
}
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل حساب</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #0f2027;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            color: #fff;
        }

        .box {
            width: 100%;
            max-width: 500px;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            padding: 40px 30px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #45ffca;
        }

        h3 {
            text-align: center;
            margin-bottom: 25px;
            font-size: 32px;
            color: #ffcc00;
            font-weight: bold;
            text-shadow: 2px 2px 8px rgba(255, 255, 255, 0.6);
            letter-spacing: 1px;
        }

        .inputbox {
            margin-bottom: 15px;
        }

        .inputbox input {
            width: 100%;
            padding: 12px 14px;
            border: none;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 14px;
            outline: none;
            transition: background 0.3s;
        }

        .inputbox input::placeholder {
            color: #ccc;
        }

        .inputbox input:focus {
            background: rgba(255, 255, 255, 0.3);
        }

        input[type="submit"] {
            background-color: #45ffca;
            color: #1b1b1b;
            font-weight: bold;
            border: none;
            padding: 12px;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s ease;
            width: 100%;
            margin-top: 15px;
        }

        input[type="submit"]:hover {
            background-color: #36cca5;
        }

        .link {
            text-align: center;
            margin-top: 15px;
        }

        .link a {
            color: #fff;
            text-decoration: underline;
            font-size: 14px;
        }

        .link a:hover {
            text-decoration: none;
        }

        .error {
            background: #ff5e5e;
            color: white;
            text-align: center;
            padding: 10px;
            margin: 15px auto;
            border-radius: 8px;
        }

        /* تنسيق الرسالة الناجحة */
        .success {
            background: linear-gradient(135deg, #45ffca, #36cca5);
            color: #1b1b1b;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 255, 255, 0.5);
            max-width: 400px;
            margin: 20px auto;
        }

    </style>
</head>
<body>

    <!-- إذا كان هناك رسالة شكر -->
    <?php if ($SuccessMessage): ?>
        <div class="box">
            <?php echo $SuccessMessage; ?>
        </div>

        <!-- إضافة جافا سكريبت للتحويل بعد 3 ثوانٍ -->
        <script>
            setTimeout(function() {
                window.location.href = 'informationcaptain.php';
            }, 3000); // الانتظار لمدة 3 ثوانٍ ثم التحويل إلى صفحة تسجيل الدخول
        </script>

    <?php else: ?>
        <!-- إذا لم يتم التسجيل بعد -->
        <div class="box">
            <form action="" method="POST">
                <h2>تسجيل حساب للانضمام الى فريق القائد</h2>
                <h3>عمر حنظل</h3>

                <!-- عرض رسالة الخطأ في حال كان هناك -->
                <?php echo @$ErrorMessage; ?>

                <div class="inputbox">
                    <input name="Get01" type="text" required="required" placeholder="اسم المستخدم"/>
                </div>

                <div class="inputbox">
                    <input name="Get03" type="text" required="required" placeholder="رقم الهاتف"/>
                </div>

                <div class="inputbox">
                    <input name="Get04" type="password" placeholder="كلمة المرور" required="required"/>
                </div>

                <div class="link">
                    <a href="login.php">تسجيل دخول</a>
                </div>

                <input type="submit" name="singup" value="تسجيل حساب">
            </form>
        </div>
    <?php endif; ?>

</body>
</html>
