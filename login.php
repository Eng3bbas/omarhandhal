<?php
session_start();
$username = $_SESSION['username'] ?? '';

require 'config.php';
global $conn;

$Post01 = $_POST['Get01'] ?? '';
$Post02 = $_POST['Get02'] ?? '';
$Error = '';

if (isset($_POST['login'])) {
    if (empty($Post01) || empty($Post02)) {
        $Error = '<p class="error">لا يمكن ترك الحقول فارغة</p>';
    } else {
        $select = "SELECT * FROM users WHERE username = $1";
        $RunSelect = pg_query_params($conn, $select, [$Post01]);

        if (pg_num_rows($RunSelect) > 0) {
            $RowSelect = pg_fetch_assoc($RunSelect);
            if ($Post02 != $RowSelect['password']) {
                $Error = '<p class="error">كلمة المرور غير صحيحة</p>';
            } else {
                $_SESSION['username'] = $RowSelect['username'];
                $_SESSION['user_id'] = $RowSelect['id'];

                echo '<link rel="stylesheet" href="style2.css">';
                echo '<div class="style3"><p>تم تسجيل الدخول بنجاح</p></div>';
                $redirect = ($RowSelect['role'] === 'admin') ? 'captains.php' : 'dashboarduser.php';
                echo "<meta http-equiv='refresh' content='2;url=$redirect'>";
                exit;
            }
        } else {
            $Error = '<p class="error">لا يوجد حساب بهذا الاسم</p>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول</title>
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

    </style>
</head>
<body>

    <div class="box">
        <form action="" method="POST">
            <h2>تسجيل دخول</h2>

            <!-- عرض رسالة الخطأ إذا كان هناك -->
            <?php echo $Error; ?>

            <div class="inputbox">
                <input name="Get01" type="text" required="required" placeholder="اسم المستخدم"/>
            </div>
            <div class="inputbox">
                <input name="Get02" type="password" required="required" placeholder="كلمة المرور"/>
            </div>

            <input type="submit" name="login" value="تسجيل دخول">

            <div class="link">
                <a href="signup.php">تسجيل حساب جديد</a>
            </div>
        </form>
    </div>

</body>
</html>
