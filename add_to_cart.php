<?php
require 'config.php';
global $conn;

session_start();

$product = null;
$added = false;
$error = '';
$username = $_SESSION['username'] ?? '';
$userId = $_SESSION['user_id'] ?? 1;

// Get user profile image
$select = "SELECT * FROM members WHERE username='$username'";
$RunSelect = pg_query($conn, $select);
$RowSelect = pg_fetch_array($RunSelect);
$base64Image = $RowSelect['profileimage'] ?? '';
$_SESSION['profileimage'] = $base64Image;

if (isset($_GET['product_id'])) {
    $productId = (int)$_GET['product_id'];

    $query = "SELECT * FROM products WHERE id = $productId";
    $result = pg_query($conn, $query);

    if ($result && pg_num_rows($result) > 0) {
        $product = pg_fetch_assoc($result);
        $availableQty = (int)$product['quantity'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $quantity = (int)$_POST['quantity'];
            $price = $product['price'];

            if ($quantity > $availableQty || $quantity <= 0) {
                $error = '❌ الكمية المختارة غير متوفرة.';
            } else {
                $insert = "INSERT INTO cart (user_id, product_id, quantity, price) 
                           VALUES ($userId, $productId, $quantity, $price)";
                pg_query($conn, $insert);
                $added = true;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إضافة إلى السلة</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
  font-family: 'Arial', sans-serif;
  background-color: #f0f2f5;
  margin: 0;
  padding: 0;
  direction: rtl;
}

        /* زر القائمة الجانبية */
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
  width: 180px;
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
  width: 120px;
  height: 120px;
  margin: 0 auto 15px;
  border-radius: 50%;
  overflow: hidden;
  display: flex;
  justify-content: center;
  align-items: center;
  background-color: #ccc; /* خلفية رمادية كإطار احتياطي */
}

.profile-img img {
  width: 120px;
  height: 120px;
  object-fit: cover; /* هذا يضمن اقتصاص الصورة داخل الدائرة */
  border-radius: 50%;
}



.profile-img:hover {
  transform: scale(1.05);
  box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.2);
}

.sidebar h2 {
  text-align: center;
  font-size: 20px;
  margin: 10px 0 20px;
}

.main-content {
  transition: margin-right 0.3s ease;
}


        .main-content {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px;
            flex-direction: column;
            margin-right: 0;
            transition: margin-right 0.3s ease;
        }

        .product-box {
            background: #fff;
            padding: 30px;
            margin: 30px auto;
            max-width: 600px;
            border-radius: 15px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            text-align: center;
            width: 100%;
        }

        .product-box h2 {
            font-size: 24px;
            color: #333;
        }

        .product-box p {
            font-size: 18px;
            margin: 10px 0;
        }

        .product-box input[type="number"] {
            width: 80px;
            padding: 6px;
            font-size: 16px;
            text-align: center;
            margin-top: 10px;
            color: black;
        }

        .product-box button {
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            background-color: #4CAF50;
            color: white;
            border-radius: 10px;
            cursor: pointer;
        }

        .product-box button:hover {
            background-color: #45a049;
        }

        .success {
            color: green;
            margin-top: 15px;
        }

        .error {
            color: red;
            margin-top: 15px;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 250px;
            }

            .main-content {
                margin-right: 0;
            }

            .menu-btn {
                display: block;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content.move-left {
                margin-right: 250px;
            }
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
        }
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
  width: 180px;
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
  width: 120px;
  height: 120px;
  margin-bottom: 15px;
  border-radius: 50%;
  overflow: hidden;
  box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.profile-img img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  pointer-events: none;
  transition: none;
}
.profile-img:hover {
  transform: scale(1.05);
  box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.2);
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

.sidebar ul li a i {
  margin-left: 10px;
  font-size: 18px;
}

.sidebar ul li a {
  display: flex;
  align-items: center;
}

.sidebar ul li a:hover i {
  color: #fff;
}


@media (max-width: 768px) {
  .main-content {
    margin-right: 0;
  }
}
.content{
    width:100%;
    margin:10px;

}.title-info{
    background-color: rgb(4,129,255);
    padding: 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-radius: 8px;
    margin: 10px 0;
}
.data-info {
    display: block; /* ensures items stack vertically */
    margin-top: 5px;




    
}
.box {
    background-color: #f4f4f4;
    border: 1px solid #ccc;
    padding: 2px;
    margin-bottom: 2px; /* spacing between boxes */
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.box i {
    font-size: 2rem;
    text-align: center;
}
.data-info .box{
    background-color:#123;
    height:70px;
    flex-basis: 50px;
    flex-grow: 1;
    border-radius:4px;
display:flex;
align-items: center;
justify-content: space-around;
}
.data-info .box i{
font-size: 40px;
}
.data-info .box.data{
    text-align: center;
}

.data-info .box .data span{
    font-size: 30px;
}
table{
    width:100%;
    text-align: center;
    border-spacing: 8px;
}
td,th{background-color: #123;
height: 40px;
border-radius: 8px;
}
th{
    background-color: #0080ff;
}

.price, .count{
    padding:6px;
    border-radius: 6px;
}
.price{
    background-color: green;
}.count{
    background-color: gold;
    color:black
}
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

<div class="main-content" id="main-content">
    <div class="title-info">
        <p>إضافة المنتج إلى السلة</p>
        <i class="fas fa-cart-plus"></i>
    </div>

    <?php if ($product): ?>
        <div class="product-box">
            <h2><?php echo htmlspecialchars($product['product_name']); ?></h2>
            <p>السعر: <?php echo htmlspecialchars($product['price']); ?> د.ع</p>
            <p style="color: black;">الكمية المتاحة: <?php echo htmlspecialchars($product['quantity']); ?></p>

            <?php if ($added): ?>
                <a class="success" href="view_cart.php">عرض فاتورةالشراء</a>
                <p class="success">✅ تم إضافة المنتج إلى السلة بنجاح!</p>  
                <a class="success" href="order_products.php">رجوع الى المنتجات</a>

            <?php elseif ($error): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>

            <?php if (!$added): ?>
                <form method="POST">
                    <label for="quantity">الكمية:</label>
                    <br>
                    <input type="number" name="quantity" id="quantity" min="1"
                           max="<?php echo (int)$product['quantity']; ?>" value="1" required>
                    <br>
                    <button type="submit">أضف إلى السلة</button>
                    <a href="order_products.php">رجوع</a>
                </form>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="product-box">
            <p>❌ لم يتم العثور على المنتج.</p>
        </div>
    <?php endif; ?>
</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        sidebar.classList.toggle('show');
        mainContent.classList.toggle('move-left');
    }
</script>

</body>
</html>
