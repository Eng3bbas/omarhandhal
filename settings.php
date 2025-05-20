<?php
require 'config.php';
session_start();
global $conn;

$username = $_SESSION['username'] ?? '';

// Get user data
$select = "SELECT * FROM members WHERE username='$username'";
$RunSelect = pg_query($conn, $select);
$RowSelect = pg_fetch_array($RunSelect);
$base64Image = $RowSelect['profileimage'];
$_SESSION['profileimage'] = $base64Image;

// Handle form submission
if (isset($_POST['update_info'])) {
    $userAge = (int)$_POST['user_age'];
    $userLength = (int)$_POST['user_length'];
    $userWeight = (int)$_POST['user_weight'];
    $userStatus = pg_escape_string($_POST['user_status']);
    $userChild = (int)$_POST['user_child'];
    $gender = pg_escape_string($_POST['gender']);
    $activityLevel = pg_escape_string($_POST['activity_level']);

    $updateQuery = "UPDATE members SET
        user_age = $userAge,
        user_length = $userLength,
        user_weight = $userWeight,
        user_status = '$userStatus',
        user_child = $userChild,
        gender = '$gender',
        activity_level = '$activityLevel'
        WHERE username = '$username'";

    $updateResult = pg_query($conn, $updateQuery);

    if ($updateResult) {
        echo "<script>alert('تم تحديث البيانات بنجاح.'); window.location.href = 'settings.php';</script>";
    } else {
        echo "<script>alert('فشل في تحديث البيانات.'); window.location.href = 'settings.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>الإعدادات</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
            direction: rtl;
        }

        .menu-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            font-size: 35px;
            color: #fff;
            background-color: #3498db;
            padding: 12px 18px;
            border-radius: 50%;
            cursor: pointer;
            z-index: 1100;
        }

        .sidebar {
            position: fixed;
            top: 0;
            right: 0;
            width: 250px;
            height: 100%;
            background-color: #34495e;
            color: white;
            padding: 20px;
            transform: translateX(100%);
            transition: transform 0.3s ease-in-out;
            z-index: 1000;
        }

        .sidebar.show {
            transform: translateX(0);
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            padding: 18px;
            border-bottom: 1px solid #7f8c8d;
        }

        .sidebar ul li a {
            color: #ecf0f1;
            text-decoration: none;
            font-size: 18px;
            display: flex;
            align-items: center;
        }

        .sidebar ul li a i {
            margin-left: 10px;
        }

        .profile-img {
            width: 100px;
            height: 100px;
            margin: 0 auto 10px;
            border-radius: 50%;
            overflow: hidden;
        }

        .profile-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .sidebar h2 {
            text-align: center;
            font-size: 18px;
            margin-bottom: 20px;
        }

        .main-content {
            margin-left: 250px;
            padding: 40px;
            transition: margin-left 0.3s ease, transform 0.3s ease;
        }

        .title-info {
            font-size: 24px;
            margin-bottom: 20px;
            color: #2c3e50;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        form {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            max-width: 700px;
            margin: 0 auto; /* لضمان بقاء المحتوى في المنتصف */
        }

        table {
            width: 100%;
        }

        table td {
            padding: 10px;
            vertical-align: middle;
        }

        input[type="text"],
        input[type="number"],
        select {
            width: 100%;
            padding: 8px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button[type="submit"] {
            background-color: #3498db;
            color: white;
            padding: 12px 20px;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 20px;
        }

        button[type="submit"]:hover {
            background-color: #2980b9;
        }

        /* الشاشات الكبيرة */
        @media (min-width: 769px) {
            .sidebar {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 250px;
                padding: 40px;
            }

            .menu-btn {
                display: none;
            }

            /* مركزية المحتوى */
            .main-content {
                display: flex;
                justify-content: center;
                align-items: center;
            }

            form {
                margin: 0 auto;
                max-width: 800px;
            }
        }

        /* الشاشات الصغيرة */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 20px;
            }

            .menu-btn {
                display: block;
            }

            .sidebar {
                width: 200px;
                height: 100%;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content.open {
                transform: translateX(-200px); /* تحريك المحتوى إلى اليسار مع القائمة */
            }

            form {
                width: 90%;
                margin: auto;
                padding: 15px;
            }

            table td {
                display: block;
                width: 100%;
                padding: 8px 0;
            }

            table tr {
                display: block;
                margin-bottom: 10px;
            }

            button[type="submit"] {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<!-- زر القائمة الجانبية -->
<div class="menu-btn" onclick="toggleSidebar()">☰</div>

<!-- القائمة الجانبية -->
<div class="sidebar" id="sidebar">
    <ul>
        <li class="profile">
            <div class="profile-img">
                <?php if (!empty($base64Image)): ?>
                    <img src="data:image/png;base64,<?php echo $base64Image; ?>" alt="Profile Image">
                <?php else: ?>
                    <p>لا توجد صورة</p>
                <?php endif; ?>
            </div>
            <h2><?php echo htmlspecialchars($username); ?></h2>
        </li>
        <li><a href="dashboarduser.php"><i class="fas fa-home"></i>الرئيسية</a></li>
        <li><a href="display_course.php"><i class="fas fa-book"></i>الكورسات</a></li>
        <li><a href="order_products.php"><i class="fas fa-box"></i>المنتجات</a></li>
        <li><a href="view_cart.php"><i class="fas fa-shopping-cart"></i>السلة</a></li>
        <li><a class="active" href="settings.php"><i class="fas fa-cogs"></i>الإعدادات</a></li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i>تسجيل الخروج</a></li>
    </ul>
</div>

<!-- المحتوى الرئيسي -->
<div class="main-content" id="main-content">
    <div class="title-info">
        <i class="fas fa-edit"></i>
        <span>تحديث المعلومات الشخصية</span>
    </div>

    <form action="settings.php" method="POST">
        <table>
            <tr>
                <td>الاسم:</td>
                <td><input type="text" name="username" value="<?php echo $RowSelect['username']; ?>" disabled></td>
            </tr>
            <tr>
                <td>العمر:</td>
                <td><input type="number" name="user_age" value="<?php echo $RowSelect['user_age']; ?>" required></td>
            </tr>
            <tr>
                <td>الطول (سم):</td>
                <td><input type="number" name="user_length" value="<?php echo $RowSelect['user_length']; ?>" required></td>
            </tr>
            <tr>
                <td>الوزن (كغم):</td>
                <td><input type="number" name="user_weight" value="<?php echo $RowSelect['user_weight']; ?>" required></td>
            </tr>
            <tr>
                <td>الحالة الاجتماعية:</td>
                <td>
                    <select name="user_status" required>
                        <option value="single" <?php if ($RowSelect['user_status'] == 'single') echo 'selected'; ?>>أعزب</option>
                        <option value="married" <?php if ($RowSelect['user_status'] == 'married') echo 'selected'; ?>>متزوج</option>
                        <option value="divorced" <?php if ($RowSelect['user_status'] == 'divorced') echo 'selected'; ?>>مطلق</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>عدد الأطفال:</td>
                <td><input type="number" name="user_child" value="<?php echo $RowSelect['user_child']; ?>" required></td>
            </tr>
            <tr>
                <td>الجنس:</td>
                <td>
                    <select name="gender" required>
                        <option value="M" <?php if ($RowSelect['gender'] == 'M') echo 'selected'; ?>>ذكر</option>
                        <option value="F" <?php if ($RowSelect['gender'] == 'F') echo 'selected'; ?>>أنثى</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>النشاط البدني:</td>
                <td>
                    <select name="activity_level" required>
                        <option value="1" <?php if ($RowSelect['activity_level'] == 1) echo 'selected'; ?>>قليل</option>
                        <option value="2" <?php if ($RowSelect['activity_level'] == 2) echo 'selected'; ?>>متوسط</option>
                        <option value="3" <?php if ($RowSelect['activity_level'] == 3) echo 'selected'; ?>>مرتفع</option>
                        <option value="4" <?php if ($RowSelect['activity_level'] == 4) echo 'selected'; ?>>نشيط جدًا</option>
                        <option value="5" <?php if ($RowSelect['activity_level'] == 5) echo 'selected'; ?>>محترف</option>
                    </select>
                </td>
            </tr>
        </table>

        <button type="submit" name="update_info">تحديث</button>
    </form>
</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById("sidebar");
        const mainContent = document.getElementById("main-content");

        sidebar.classList.toggle("show");
        mainContent.classList.toggle("open");  // إضافة أو إزالة التحريك من المحتوى
    }
</script>

</body>
</html>
