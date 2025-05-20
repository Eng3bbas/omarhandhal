<?php
session_start();
require 'config.php';
global $conn;

$userId = $_SESSION['user_id'] ?? '';
$username = $_SESSION['username'] ?? '';
$profileImage = $_SESSION['profileimage'] ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $courseType = $_POST['course_type'] ?? '';
    if ($userId && !empty($courseType)) {
        $insertOrderQuery = "INSERT INTO course_orders (user_id, course_type) VALUES ($userId, '$courseType')";
        $insertResult = pg_query($conn, $insertOrderQuery);
        if ($insertResult) {
            echo "<script>alert('تم تقديم كورس بنجاح!');</script>";
        } else {
            echo "حدث خطأ أثناء تقديم الطلب. الرجاء المحاولة مرة أخرى.<br>";
        }
    } else {
        echo "يرجى اختيار نوع الكورس!<br>";
    }
}

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>طلب كورس</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    

        body {
            margin: 0;
            background-color: #ecf0f1;
            font-family: 'Roboto', sans-serif;
        }

        /* القائمة الجانبية */
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

        .menu-btn:hover {
            background-color: #2980b9;
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
            display: block;
            text-align: right;
        }

        .sidebar ul li a:hover {
            color: #fff;
            text-decoration: underline;
        }

        .profile-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            overflow: hidden;
            margin: auto;
        }

        .profile-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .sidebar h2 {
            text-align: center;
            margin-top: 10px;
        }

        .overlay {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 900;
            display: none;
        }

        .overlay.show {
            display: block;
        }

        /* محتوى الكورسات */
        .main-content {
            padding: 40px;
            margin-right: 0;
            transition: margin-right 0.3s ease;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .course-buttons {
            background-color: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 320px;
        }

        .course-buttons h2 {
            margin-bottom: 30px;
            font-size: 22px;
            color: #2c3e50;
        }

        .course-buttons form {
            margin-bottom: 20px;
        }

        .course-buttons button {
            padding: 15px 30px;
            font-size: 18px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 8px;
            transition: background-color 0.3s;
            cursor: pointer;
            width: 100%;
        }

        .course-buttons button:hover {
            background-color: #2980b9;
        }

        .course-buttons a {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #3498db;
            font-weight: bold;
        }

        .course-buttons a:hover {
            color: #2980b9;
        }

        @media (min-width: 769px) {
            .sidebar {
                transform: translateX(0);
            }

            .main-content {
                margin-right: 250px;
            }

            .menu-btn {
                display: none;
            }

            .overlay {
                display: none !important;
            }
        }
    </style>
</head>
<body>

<!-- زر القائمة -->
<div class="menu-btn" onclick="toggleSidebar()">☰</div>

<!-- القائمة الجانبية -->
<div class="sidebar" id="sidebar">
    <ul>
        <li class="profile">
            <div class="profile-img">
                <?php if (!empty($profileImage)): ?>
                    <img src="data:image/png;base64,<?php echo $profileImage; ?>" alt="Profile Image"/>
                <?php else: ?>
                    <p>No profile image</p>
                <?php endif; ?>
            </div>
            <h2><?php echo htmlspecialchars($username); ?></h2>
        </li>
        <li><a href="dashboarduser.php"><i class="fas fa-tachometer-alt"></i> لوحة التحكم</a></li>
        <li><a href="display_course.php"><i class="fas fa-book"></i> الكورسات</a></li>
        <li><a href="order_products.php"><i class="fas fa-box"></i> المنتجات</a></li>
        <li><a href="view_cart.php"><i class="fas fa-shopping-cart"></i> السلة</a></li>
        <li><a href="settings.php"><i class="fas fa-cogs"></i> الإعدادات</a></li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a></li>
    </ul>
</div>

<div class="overlay" id="overlay" onclick="toggleSidebar()"></div>

<!-- محتوى الصفحة -->
<div class="main-content">
    <div class="course-buttons">
        <h2>اختر نوع الكورس الذي ترغب في طلبه</h2>

        <form method="POST" action="">
            <input type="hidden" name="course_type" value="غذائي">
            <button type="submit">
                <i class="fas fa-apple-alt"></i> طلب كورس غذائي
            </button>
        </form>

        <form method="POST" action="">
            <input type="hidden" name="course_type" value="تمارين">
            <button type="submit">
                <i class="fas fa-dumbbell"></i> طلب كورس تمارين
            </button>
        </form>

        <a href="dashboarduser.php"><i class="fas fa-arrow-right"></i> رجوع</a>
    </div>
</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById("sidebar");
        const overlay = document.getElementById("overlay");
        sidebar.classList.toggle("show");
        overlay.classList.toggle("show");
    }
    <button onclick="window.history.back();">رجوع</button>

</script>

</body>
</html>
