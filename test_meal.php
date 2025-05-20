
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>اختيار وجبات التغذية</title>
    <link rel="stylesheet" href="style4.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Arial';
            direction: rtl;
            margin: 0;
            padding: 0;
        }

        .content {
            max-width: 1000px;
            margin: auto;
            padding: 30px;
        }

        .title-info p {
            font-size: 30px;
            color: #333;
            text-align: center;
        }

        .meal-category {
            background: #fff;
            margin: 20px 0;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 6px #ccc;
        }

        .meal-category h4 {
            font-size: 22px;
            color: #0056b3;
        }

        .meal label {
            font-size: 18px;
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            color: black;
        }

        .meal input {
            margin-left: 10px;
            transform: scale(1.2);
        }

        button[type="submit"] {
            background-color: #0056b3;
            color: white;
            padding: 15px 25px;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            cursor: pointer;
            margin-top: 30px;
        }

        button[type="submit"]:hover {
            background-color: #003366;
        }
    </style>
</head>
<body>

<div class="menu">
    <ul>
        <li class="profile">
            <div class="img-box"><img src="10.jpeg" alt="profile"></div>
            <h2>OmarHandhal</h2></li>
        <li><a href="captains.php"><i class="fas fa-home"></i>
                <p>Dashboard</p></a></li>
        <li><a href="display_members.php"><i class="fas fa-user-group"></i>
                <p>المتدربين</p></a></li>
        <li><a href="display_products.php"><i class="fas fa-table"></i>
                <p>المنتجات</p></a></li>
        <li><a href="#"><i class="fas fa-table"></i>
                <p>طلبات المتدربين</p></a></li>
        <li class="log-out"><a href="logout.php"><i class="fas fa-sign-out"></i>
                <p>تسجيل خروج</p></a></li>
    </ul>
</div>

<div class="content">
    <div class="title-info">
        <p>اختيار وجبات التغذية</p>
    </div>