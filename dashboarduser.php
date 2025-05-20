<?php
require 'config.php';
global $conn;
session_start();

$username = $_SESSION['username'] ?? '';

// استعلام لجلب بيانات المستخدم
$select = "SELECT * FROM members WHERE username='$username'";
$RunSelect = pg_query($conn, $select);
$RowSelect = pg_fetch_array($RunSelect);

// تحويل الجنس من قيمة "M" أو "F" إلى "ذكر" أو "أنثى"
$gender = $RowSelect['gender']; // جلب قيمة الجنس
if ($gender == 'M') {
    $genderText = 'ذكر';
} elseif ($gender == 'F') {
    $genderText = 'أنثى';
} else {
    $genderText = 'غير محدد'; // حالة احتياطية إذا كانت القيمة غير معروفة
}

// تحويل النشاط البدني من أرقام إلى نصوص
$activity = $RowSelect['activity_level']; // جلب قيمة النشاط البدني
switch ($activity) {
    case 1:
        $activityText = 'قليل';
        break;
    case 2:
        $activityText = 'متوسط';
        break;
    case 3:
        $activityText = 'مرتفع';
        break;
    case 4:
        $activityText = 'نشط جدا';
        break;
    case 5:
        $activityText = 'محترف';
        break;
    default:
        $activityText = 'غير محدد';
        break;
}
// حساب السعرات الحرارية اليومية (TDEE)
$weight = $RowSelect['user_weight']; // بالكيلو
$height = $RowSelect['user_length']; // بالسنتيمتر
$age    = $RowSelect['user_age'];
$gender = $RowSelect['gender'];
$activity = $RowSelect['activity_level'];

// حساب BMR
if ($gender == 'M') {
    $bmr = (10 * $weight) + (6.25 * $height) - (5 * $age) + 5;
} elseif ($gender == 'F') {
    $bmr = (10 * $weight) + (6.25 * $height) - (5 * $age) - 161;
} else {
    $bmr = 0;
}

// معامل النشاط
switch ($activity) {
    case 1: $factor = 1.2; break;
    case 2: $factor = 1.375; break;
    case 3: $factor = 1.55; break;
    case 4: $factor = 1.725; break;
    case 5: $factor = 1.9; break;
    default: $factor = 1.2; break;
}

$calories = round($bmr * $factor);


// تخزين صورة الملف الشخصي
$base64Image = $RowSelect['profileimage'];
$_SESSION['profileimage'] = $base64Image;

// استعلام لحساب عدد المنتجات
$productCountQuery = "SELECT COUNT(*) AS total FROM products";
$productCountResult = pg_query($conn, $productCountQuery);
$productCountRow = pg_fetch_assoc($productCountResult);
$productCount = $productCountRow['total'];
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>لوحة التحكم</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
     body {
      font-family: 'Arial', sans-serif;
      background-color: #f0f2f5;
      margin: 0;
      padding: 0;
      direction: rtl;
    }

    /* الزر الخاص بفتح القائمة */
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
      transition: 0.3s ease;
    }

    .menu-btn:hover {
      background-color: #2980b9;
    }

    /* القائمة الجانبية */
     .sidebar {
     position: fixed;
  top: 0;
  right: 0;
  width: 180px; /* عرض المنيو الجديد */
      height: 100%;
      background-color: #34495e;
      color: white;
      padding: 20px;
      transform: translateX(100%);
      transition: transform 0.3s ease-in-out;
      z-index: 1000;
      box-shadow: 2px 0 10px rgba(0, 0, 0, 0.2);
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
      transition: background-color 0.3s ease, padding-left 0.3s ease;
    }

    .sidebar ul li:hover {
      background-color: #2980b9;
      padding-left: 25px;
      border-radius: 8px;
    }

    .sidebar ul li a {
      color: #ecf0f1;
      text-decoration: none;
      font-size: 18px;
      display: flex;
      align-items: center;
      text-align: right;
    }

    .sidebar ul li a i {
      margin-left: 10px;
      font-size: 18px;
    }

    /* محتوى الصفحة */
    .main-content {
      padding: 20px;
      margin-right: 0;
      transition: margin-right 0.3s ease;
    }

    .main-content h1 {
      color: #2c3e50;
    }

    .profile-info {
      margin-top: 30px;
    }

    /* الخلفية المظللة عند فتح المنيو */
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

    /* تصميم الصورة بشكل جميل */
    .profile-img {
      width: 120px;  /* تحديد عرض مناسب */
      height: 120px;
      margin-bottom: 15px;
      border-radius: 50%;
      overflow: hidden; /* لتقليل أي تداخل بالصورة */
      box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);  /* إضافة ظل ناعم للصورة */
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .profile-img img {
      width: 100%;
      height: 100%;
      object-fit: cover;  /* تأكد من أن الصورة تملأ العنصر بشكل مناسب */
    }

    .profile-img:hover {
      transform: scale(1.05);  /* تكبير الصورة عند التمرير عليها */
      box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.2);  /* زيادة الظل عند التمرير */
    }

    .box {
      padding: 20px;
      background-color: #fff;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      margin-top: 20px;
      text-align: center;
      cursor: pointer;
      border-radius: 8px;
      transition: background-color 0.3s, transform 0.3s;
    }

    .box:hover {
      background-color: #3498db;
      color: #fff;
      transform: translateY(-5px);
    }

    .box i {
      font-size: 40px;
      margin-bottom: 10px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th, td {
      padding: 15px;
      border: 1px solid #ddd;
      text-align: center;
    }

    th {
      background-color: #3498db;
      color: #fff;
    }

    @media (min-width: 769px) {
      .menu-btn {
        display: none; /* إخفاء زر المنيو على الشاشات الكبيرة */
      }

     /* القائمة الجانبية */
/* القائمة الجانبية */
.sidebar {
  position: fixed;
  top: 0;
  right: 0;
  width: 200px;  /* عرض المنيو هنا 200px */
  height: 100%;
  background-color: #34495e;
  color: white;
  padding: 20px;
  transform: translateX(100%); /* مخفي بشكل افتراضي */
  transition: transform 0.3s ease-in-out;
  z-index: 1000;
  box-shadow: 2px 0 10px rgba(0, 0, 0, 0.2);
}

/* عند إضافة الكلاس show يتم فتح المنيو في الشاشات الصغيرة */
.sidebar.show {
  transform: translateX(0);
}

/* قائمة تظهر دائمًا في الشاشات الكبيرة */
@media (min-width: 769px) {
  .sidebar {
    transform: translateX(0); /* تظهر المنيو بشكل دائم */
  }

  .main-content {
    margin-right: 200px; /* تأكيد وجود مساحة للمحتوى الرئيسي */
  }

  .menu-btn {
    display: none; /* إخفاء زر المنيو في الشاشات الكبيرة */
  }
}

@media (max-width: 768px) {
  .main-content {
    margin-right: 0;
  }
}

.sidebar ul li a i {
  margin-left: 10px;  /* المسافة بين الأيقونة والنص */
  font-size: 18px;    /* حجم الأيقونة */
}

.sidebar ul li a {
  display: flex;        /* لضمان محاذاة الأيقونة والنص بشكل صحيح */
  align-items: center;  /* محاذاة الأيقونة والنص في الوسط */
}

.sidebar ul li a:hover i {
  color: #fff;          /* تغيير لون الأيقونة عند التمرير */
}

      .overlay {
        display: none !important;
      }

      .main-content {
        margin-right: 250px;
      }
    }
/* إزالة أي تأثيرات على الصورة عند التمرير */
.profile-img img {
  pointer-events: none; /* عدم التفاعل مع الصورة */
  transition: none; /* إلغاء أي تأثير انتقالي */
}

.profile-img:hover {
  transform: none; /* إيقاف تأثير التكبير */
  box-shadow: none; /* إيقاف الظل عند التمرير */
}



    @media (max-width: 768px) {
      .main-content {
        margin-right: 0;
      }
    }
  </style>
</head>
<body>

<!-- زر فتح القائمة -->
<div class="menu-btn" onclick="toggleSidebar()">☰</div>

<!-- القائمة الجانبية -->
<div class="sidebar" id="sidebar">
  <ul>
    <li class="profile">
      <div class="profile-img">
        <?php if (!empty($base64Image)): ?>
          <img src="data:image/png;base64,<?php echo $base64Image; ?>" alt="Profile Image"/>
        <?php else: ?>
          <p>No profile image available.</p>
        <?php endif; ?>
      </div>
      <h2><?php echo htmlspecialchars($username); ?></h2>
    </li>
    <li><a href="#"><i class="fas fa-tachometer-alt"></i> لوحة التحكم</a></li>  <!-- لوحة التحكم -->
    <li><a href="display_course.php"><i class="fas fa-book"></i> الكورسات</a></li> <!-- الكورسات -->
    <li><a href="order_products.php"><i class="fas fa-cogs"></i> المنتجات</a></li> <!-- المنتجات -->
    <li><a href="view_cart.php"><i class="fas fa-shopping-cart"></i> السلة</a></li> <!-- السلة -->
    <li><a href="settings.php"><i class="fas fa-cogs"></i> الإعدادات</a></li> <!-- الإعدادات -->
    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a></li> <!-- تسجيل الخروج -->
  </ul>
</div>

<!-- خلفية عند فتح القائمة على الجوال -->
<div class="overlay" id="overlay" onclick="toggleSidebar()"></div>

<!-- محتوى الصفحة -->
<div class="main-content">
  <h1>أهلاً بك في لوحة التحكم</h1>

  <!-- معلومات المستخدم -->
  <div class="profile-info">
    <h3>المعلومات الشخصية</h3>
    <table>
     <thead>
  <tr>
    <th>الاسم</th>
    <th>العمر</th>
    <th>الطول</th>
    <th>الوزن</th>
    <th>الحالة الاجتماعية</th>
    <th>عدد الأطفال</th>
    <th>الجنس</th>
    <th>النشاط البدني</th>
    <th>السعرات الحرارية اليومية</th>
  </tr>
</thead>
      <tbody>
  <tr>
    <td><?php echo htmlspecialchars($RowSelect['username']); ?></td>
    <td><?php echo htmlspecialchars($RowSelect['user_age']); ?></td>
    <td><?php echo htmlspecialchars($RowSelect['user_length']); ?></td>
    <td><?php echo htmlspecialchars($RowSelect['user_weight']); ?></td>
    <td><?php echo htmlspecialchars($RowSelect['user_status']); ?></td>
    <td><?php echo htmlspecialchars($RowSelect['user_child']); ?></td>
    <td><?php echo $genderText; ?></td>
    <td><?php echo $activityText; ?></td>
    <td><?php echo $calories . ' سعرة'; ?></td>
  </tr>
</tbody>
    </table>
  </div>

  <!-- الأقسام الأخرى -->
  <div class="box" onclick="window.location.href='display_course.php';">
    <i class="fas fa-graduation-cap"></i>
    <p>الكورسات</p>
  </div>
  <div class="box" onclick="window.location.href='order_products.php';">
    <i class="fas fa-box"></i>
    <p>المنتجات</p>
    <span><?php echo $productCount; ?></span>
  </div>
  <div class="box" onclick="window.location.href='order_course.php';">
    <i class="fas fa-cart-plus"></i>
    <p>طلب كورس</p>
  </div>

</div>

<script>
  function toggleSidebar() {
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("overlay");
    sidebar.classList.toggle("show");
    overlay.classList.toggle("show");
  }
</script>

</body>
</html>
