<?php
require 'config.php';
global $conn;

session_start();

$username = $_SESSION['username'] ?? '';

$select = "SELECT * FROM members WHERE username='$username'";
$RunSelect = pg_query($conn, $select);
$RowSelect = pg_fetch_array($RunSelect);
$base64Image = $RowSelect['profileimage'];
$_SESSION['profileimage'] = $base64Image;
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>عرض المنتجات</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    body {
      font-family: 'Arial', sans-serif;
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
      transition: 0.3s ease;
    }

    .menu-btn:hover {
      background-color: #2980b9;
    }

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
    .sidebar h2 {
      text-align: center;
      font-size: 20px;
      margin: 10px 0 20px;
    }

   .main-content {
  transition: margin-right 0.3s ease;
}

/* عندما تكون القائمة ظاهرة في الشاشات الكبيرة */
@media (min-width: 769px) {
  .sidebar {
    width: 180px;
    transform: translateX(0);
  }

  .main-content {
    margin-right: 180px;
  }
}

/* في الشاشات الصغيرة - عند إظهار القائمة */
.sidebar.show ~ .main-content {
  margin-right: 180px;
}

    .title-info {
      font-size: 24px;
      margin-bottom: 20px;
      color: #2c3e50;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .title-info i {
      color: #2980b9;
      font-size: 26px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      background-color: white;
      border-radius: 8px;
      overflow: hidden;
    }

    table th, table td {
      padding: 15px;
      text-align: center;
      border-bottom: 1px solid #ddd;
    }

    table th {
      background-color: #2980b9;
      color: white;
    }

    table td a {
      color: #2980b9;
      text-decoration: none;
      font-weight: bold;
    }

    table td a:hover {
      text-decoration: underline;
    }

    .footer {
      background-color: #2f3640;
      padding: 12px 0;
      text-align: center;
      color: white;
      font-size: 14px;
      margin-top: 40px;
    }

    @media (min-width: 769px) {
      .sidebar {
        transform: translateX(0);
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
      }}

    
  </style>
</head>
<body>

<!-- زر القائمة للجوال -->
<div class="menu-btn" onclick="toggleSidebar()">☰</div>

<!-- القائمة الجانبية -->
<div class="sidebar" id="sidebar">
  <ul>
    <li class="profile">
      <div class="profile-img">
        <?php if (!empty($base64Image)): ?>
          <img src="data:image/png;base64,<?php echo $base64Image; ?>" alt="Profile Image"/>
        <?php else: ?>
          <p>No image</p>
        <?php endif; ?>
      </div>
      <h2><?php echo htmlspecialchars($username); ?></h2>
    </li>
    <li><a href="dashboarduser.php"><i class="fas fa-home"></i>الرئيسية</a></li>
    <li><a href="display_course.php"><i class="fas fa-book"></i>الكورسات</a></li>
    <li><a class="active" href="order_products.php"><i class="fas fa-box"></i>المنتجات</a></li>
    <li><a href="view_cart.php"><i class="fas fa-shopping-cart"></i>السلة</a></li>
    <li><a href="settings.php"><i class="fas fa-cogs"></i>الإعدادات</a></li>
    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i>تسجيل الخروج</a></li>
  </ul>
</div>

<!-- محتوى الصفحة -->
<div class="main-content">
  <div class="title-info">
    <p>عرض المنتجات</p>
    <i class="fas fa-box"></i>
  </div>

  <table>
    <thead>
      <tr>
        <th>اسم المنتج</th>
        <th>السعر</th>
        <th>الكمية</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $selectProducts = "SELECT * FROM products";
      $runProducts = pg_query($conn, $selectProducts);

      if (pg_num_rows($runProducts) > 0) {
        while ($row = pg_fetch_assoc($runProducts)) {
          echo '<tr>';
          echo '<td><a href="add_to_cart.php?product_id=' . urlencode($row['id']) . '">' . htmlspecialchars($row['product_name']) . '</a></td>';
          echo '<td>' . htmlspecialchars($row['price']) . ' د.ع</td>';
          echo '<td>' . htmlspecialchars($row['quantity']) . '</td>';
          echo '</tr>';
        }
      } else {
        echo '<tr><td colspan="3">لا توجد منتجات متاحة</td></tr>';
      }
      ?>
    </tbody>
  </table>
</div>

<!-- التذييل -->
<div class="footer">
  <p>&copy; 2025 جميع الحقوق محفوظة</p>
</div>

<script>
  function toggleSidebar() {
    const sidebar = document.getElementById("sidebar");
    const mainContent = document.querySelector(".main-content");

    sidebar.classList.toggle("show");

    // أضف أو أزل الهامش للمحتوى بناءً على ظهور القائمة
    if (sidebar.classList.contains("show")) {
      mainContent.style.marginRight = "180px";
    } else {
      mainContent.style.marginRight = "0";
    }
  }
</script>

</body>
</html>
