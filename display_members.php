<?php
require 'config.php';
global $conn;

$select = "SELECT * FROM members";
$RunSelect = pg_query($conn, $select);

$members = (pg_num_rows($RunSelect) > 0) ? pg_fetch_all($RunSelect) : [];
?>

<!DOCTYPE html>
<html lang="en" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>قائمة الأعضاء</title>
    <link rel="stylesheet" href="style4.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<div class="menu">
    <ul>
        <li class="profile">
            <div class="img-box">
                <img src="10.jpeg" alt="profile">
            </div>
            <h2>OmarHandhal</h2>
        </li>
        <li><a href="captains.php"><i class="fas fa-home"></i><p>Dashboard</p></a></li>
        <li><a class="active" href="display_members.php"><i class="fas fa-user-group"></i><p>المتدربين</p></a></li>
        <li><a href="display_products.php"><i class="fas fa-table"></i><p>المنتجات</p></a></li>
        <li><a href="#"><i class="fas fa-pen"></i><p>التمارين</p></a></li>
        <li class="log-out"><a href="#"><i class="fas fa-sign-out"></i><p>تسجيل خروج</p></a></li>
    </ul>
</div>

<div class="content">
    <div class="title-info">
        <p>قائمة الأعضاء</p>
        <i class="fas fa-users"></i>
    </div>

    <div class="data-info">
        <table>
            <thead>
            <tr>
                <th>اسم المستخدم</th>
                <th>الصورة الشخصية</th>
                <th>العمر</th>
                <th>الطول (سم)</th>
                <th>الوزن (كجم)</th>
                <th>الحالة الاجتماعية</th>
                <th>عدد الأطفال</th>
                <th>الجنس</th>
                <th>النشاط البدني</th>
            </tr>
            </thead>
            <tbody>
            <?php if (count($members) > 0): ?>
                <?php foreach ($members as $member): ?>
                    <?php
                    // ترجمة القيم
                    $gender_code = $member['gender'] ?? null;
                    $activity_code = isset($member['activity_level']) ? (int)$member['activity_level'] : null;

                    $gender_text = match($gender_code) {
                        'M' => 'ذكر',
                        'F' => 'أنثى',
                        default => 'غير معرف',
                    };

                    $activity_text = match($activity_code) {
                        1 => 'قليل',
                        2 => 'متوسط',
                        3 => 'مرتفع',
                        4 => 'نشط جداً',
                        5 => 'محترف',
                        default => 'غير معرف',
                    };
                    ?>
                    <tr>
                        <td>
                            <!-- إضافة رابط لصفحة عرض الكورسات الخاصة بالعضو -->
                            <a href="members_courses.php?user_id=<?= $member['user_id']; ?>">
                                <?= htmlspecialchars($member['username']); ?>
                            </a>
                        </td>
                        <td>
                            <!-- إضافة رابط لصفحة عرض الكورسات الخاصة بالعضو عند النقر على الصورة -->
                            <a href="member_courses.php?user_id=<?= $member['user_id']; ?>">
                                <?php if (!empty($member['profileimage'])): ?>
                                    <img src="data:image/png;base64,<?= $member['profileimage']; ?>" alt="Profile Image" width="50" height="50"/>
                                <?php else: ?>
                                    <p>لا توجد صورة</p>
                                <?php endif; ?>
                            </a>
                        </td>
                        <td><?= htmlspecialchars($member['user_age']); ?></td>
                        <td><?= htmlspecialchars($member['user_length']); ?></td>
                        <td><?= htmlspecialchars($member['user_weight']); ?></td>
                        <td><?= htmlspecialchars($member['user_status']); ?></td>
                        <td><?= htmlspecialchars($member['user_child']); ?></td>
                        <td><?= $gender_text; ?></td>
                        <td><?= $activity_text; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9">لا توجد أعضاء لعرضها.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
