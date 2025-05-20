<?php
require 'config.php';
global $conn;
session_start();

if (!isset($_GET['order_id']) || intval($_GET['order_id']) <= 0) {
    echo "<script>alert('رقم الطلب غير موجود في الرابط.');</script>";
    exit();
}
$orderId = intval($_GET['order_id']);

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('يرجى تسجيل الدخول أولاً.'); window.location.href='login.php';</script>";
    exit();
}
$userId = $_SESSION['user_id'];

// جلب معلومات الطلب
$orderQuery = "SELECT * FROM course_orders WHERE order_id = $1";
$orderResult = pg_query_params($conn, $orderQuery, [$orderId]);

if ($orderResult && pg_num_rows($orderResult) > 0) {
    $orderData = pg_fetch_assoc($orderResult);
    $orderUserId = $orderData['user_id'];
} else {
    echo "<script>alert('لم يتم العثور على الطلب.'); window.location.href='display_course_orders.php';</script>";
    exit();
}

// جلب معلومات العضو المرتبط بالطلب
$memberQuery = "SELECT * FROM members WHERE user_id = $1";
$memberResult = pg_query_params($conn, $memberQuery, [$orderUserId]);

if ($memberResult && pg_num_rows($memberResult) > 0) {
    $member = pg_fetch_assoc($memberResult);

    $username = htmlspecialchars($member['username']);
    $user_age = htmlspecialchars($member['user_age']);
    $user_length = htmlspecialchars($member['user_length']);
    $user_weight = htmlspecialchars($member['user_weight']);
    $user_status = htmlspecialchars($member['user_status']);
    $user_child = htmlspecialchars($member['user_child']);

    $activity_level_code = $member['activity_level'] ?? null;
    $gender_code = $member['gender'] ?? null;

    $activity_text = match ((int)$activity_level_code) {
        1 => 'قليل',
        2 => 'متوسط',
        3 => 'مرتفع',
        4 => 'نشط جدا',
        5 => 'محترف',
        default => 'غير معرف',
    };

    $gender_text = match ($gender_code) {
        'M' => 'ذكر',
        'F' => 'أنثى',
        default => 'غير معرف',
    };
} else {
    $username = $user_age = $user_length = $user_weight = $user_status = $user_child = "غير متوفر";
    $gender_text = $activity_text = "غير معرف";
}
$categoryQuery = "SELECT * FROM food_category_group";
$categoryResult = pg_query($conn, $categoryQuery);
$categories = pg_num_rows($categoryResult) > 0 ? pg_fetch_all($categoryResult) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected_meals'])) {
    foreach ($_POST['selected_meals'] as $setNumber => $mealIds) {
        foreach ($mealIds as $mealId) {
            $rawWeight = $_POST['meal_weights'][$setNumber][$mealId] ?? '';
            $weightType = $_POST['weight_types'][$setNumber][$mealId] ?? 'gram';
            $mealName = $_POST['meal_names'][$setNumber][$mealId] ?? '';

            $weightUnit = $weightType === 'count' ? 'عدد' : 'غم';
            $weight = $rawWeight;

            $mealQuery = "SELECT name, category_id FROM food_meal WHERE id = $1";
            $mealResult = pg_query_params($conn, $mealQuery, [$mealId]);
            if (!$mealResult || !($mealData = pg_fetch_assoc($mealResult))) continue;

            $categoryId = $mealData['category_id'];
            $catNameResult = pg_query_params($conn, "SELECT name FROM food_category_group WHERE id = $1", [$categoryId]);
            $categoryName = $catNameResult ? pg_fetch_result($catNameResult, 0, 'name') : 'غير معروف';

            $insertQuery = "INSERT INTO user_meals (user_id, meal_id, meal_name, category_name, order_id, meal_number, weight, weight_unit)
                            VALUES ($1, $2, $3, $4, $5, $6, $7, $8)";
            $params = [$userId, $mealId, $mealName, $categoryName, $orderId, $setNumber, $weight, $weightUnit];
            pg_query_params($conn, $insertQuery, $params);
        }
    }

    pg_query($conn, "UPDATE course_orders SET status = 'done' WHERE order_id = $orderId");
    echo "<script>alert('تم حفظ الوجبات بنجاح!'); window.location.href='captains.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>اختيار وجبات التغذية</title>
    <style>
        body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    direction: rtl;
    background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
    background-attachment: fixed;
    margin: 0;
    padding: 30px;
    color: #f4f4f4;
}

h2 {
    text-align: center;
    color: #ffffff;
    margin-bottom: 40px;
    text-shadow: 1px 1px 4px rgba(0,0,0,0.5);
}

h3 {
    color: #ffffff;
    margin-top: 40px;
    border-bottom: 2px solid rgba(255, 255, 255, 0.2);
    padding-bottom: 5px;
}

h4 {
    color: #d1ecf1;
    margin-top: 25px;
    margin-bottom: 10px;
}

.meal {
    background: rgba(255, 255, 255, 0.95);
    border: 1px solid #ccc;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 15px;
    transition: all 0.3s ease;
    color: #333;
}

.meal:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    transform: scale(1.01);
}

.meal label {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    font-size: 17px;
}

.meal input[type="checkbox"] {
    margin-left: 10px;
    transform: scale(1.3);
}

.meal select,
.meal input[type="text"] {
    margin: 10px 0 0 10px;
    padding: 8px 10px;
    font-size: 16px;
    border: 1px solid #aaa;
    border-radius: 6px;
    min-width: 80px;
}

.meal input[type="text"]:disabled {
    background-color: #f0f0f0;
}

button {
    background-color: #1a2f43;
    color: #fff;
    border: none;
    padding: 14px 24px;
    font-size: 16px;
    border-radius: 8px;
    cursor: pointer;
    margin: 20px 5px 0 5px;
    transition: background 0.3s ease;
}

button:hover {
    background-color: #0d1e2a;
}

#preview {
    background: rgba(255, 255, 255, 0.95);
    padding: 20px;
    border-radius: 10px;
    margin-top: 30px;
    display: none;
    color: #333;
}

#preview h4 {
    color: #1a2f43;
    font-size: 20px;
    margin-bottom: 10px;
}

#preview ul {
    list-style: none;
    padding: 0;
}

#preview ul li {
    margin-bottom: 8px;
    font-size: 16px;
}

#preview ul li:first-child {
    font-weight: bold;
    color: #0d1e2a;
    margin-top: 15px;
}

@media (max-width: 768px) {
    .meal label {
        flex-direction: column;
        align-items: flex-start;
    }

    .meal select, .meal input[type="text"] {
        width: 100%;
        margin-left: 0;
    }
}

</style>
</head>
<body>

<div class="content">
 <div class="data-info" style="display: flex; flex-wrap: wrap; gap: 20px; margin-bottom: 30px;">
    <div class="box" style="flex: 1; min-width: 250px; background-color: rgba(255, 255, 255, 0.1); padding: 20px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.2);">
        <h3 style="margin-top: 0; border-bottom: 1px solid rgba(255,255,255,0.3); padding-bottom: 10px; color: #00ffff;">بيانات المتدرب</h3>
<div class="data-info" style="display: flex; flex-wrap: wrap; justify-content: center; gap: 20px; margin-bottom: 40px;">
    <?php
    $infoItems = [
        "الاسم" => $username,
        "العمر" => "$user_age سنة",
        "الطول" => "$user_length سم",
        "الوزن" => "$user_weight كغم",
        "الحالة الاجتماعية" => $user_status,
        "عدد الأولاد" => $user_child,
        "الجنس" => $gender_text,
        "النشاط البدني" => $activity_text,
    ];

    foreach ($infoItems as $label => $value): ?>
        <div style="background-color: rgba(255,255,255,0.1); border-radius: 10px; padding: 15px 20px; min-width: 160px; text-align: center; border: 1px solid rgba(255,255,255,0.2); box-shadow: 0 2px 6px rgba(0,0,0,0.2);">
            <p style="margin: 0; font-size: 15px; color: #ccc;"><?php echo $label; ?></p>
            <p style="margin: 5px 0 0; font-size: 18px; font-weight: bold; color: #fff;"><?php echo $value; ?></p>
        </div>
    <?php endforeach; ?>
</div>


<h2 style="text-align:center;">اختيار وجبات التغذية</h2>
<form method="POST" id="mealForm">
    <?php for ($i = 1; $i <= 5; $i++): ?>
        <h3>الوجبة رقم <?= $i ?></h3>
        <?php foreach ($categories as $category): ?>
            <h4><?= htmlspecialchars($category['name']) ?></h4>
            <?php
            $mealQuery = "SELECT * FROM food_meal WHERE category_id = " . intval($category['id']);
            $mealResult = pg_query($conn, $mealQuery);
            while ($meal = pg_fetch_assoc($mealResult)):
            ?>
                <div class="meal" data-set-number="<?= $i ?>">
                    <label>
                        <input type="checkbox" class="meal-checkbox"
                            name="selected_meals[<?= $i ?>][]" value="<?= $meal['id'] ?>">
                        <?= htmlspecialchars($meal['name']) ?>
                        <select name="weight_types[<?= $i ?>][<?= $meal['id'] ?>]">
                            <option value="gram">غم</option>
                            <option value="count">عدد</option>
                        </select>
                        <input type="text" name="meal_weights[<?= $i ?>][<?= $meal['id'] ?>]" placeholder="أدخل الكمية" disabled>
                        <input type="hidden" name="meal_names[<?= $i ?>][<?= $meal['id'] ?>]" value="<?= htmlspecialchars($meal['name']) ?>">
                    </label>
                </div>
            <?php endwhile; ?>
        <?php endforeach; ?>
    <?php endfor; ?>
</form>

<button type="button" id="previewButton">معاينة الوجبات</button>

<div id="preview" style="display:none;">
    <h4>معاينة الوجبات المختارة:</h4>
    <ul id="previewList"></ul>
    <button type="button" id="confirmButton">تأكيد</button>
    <button type="button" id="cancelButton">تعديل</button>
</div>

<script>
document.querySelectorAll('.meal-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function () {
        const container = this.closest('.meal');
        const input = container.querySelector('input[type="text"]');
        input.disabled = !this.checked;
        if (!this.checked) input.value = '';
    });
});

document.getElementById('previewButton').addEventListener('click', function () {
    const previewList = document.getElementById('previewList');
    previewList.innerHTML = '';
    let valid = false;
    const mealsBySet = {};

    document.querySelectorAll('.meal-checkbox:checked').forEach(cb => {
        const container = cb.closest('.meal');
        const mealName = container.querySelector('input[type="hidden"]').value;
        const weightInput = container.querySelector('input[type="text"]');
        const weightType = container.querySelector('select').value;
        const weightValue = weightInput.value.trim();
        const setNumber = container.dataset.setNumber;

        if (weightValue && parseFloat(weightValue) > 0) {
            valid = true;
            const unit = weightType === 'count' ? 'عدد' : 'غم';
            const mealInfo = `${mealName}: ${weightValue} ${unit}`;
            if (!mealsBySet[setNumber]) mealsBySet[setNumber] = [];
            mealsBySet[setNumber].push(mealInfo);
        }
    });

    if (valid) {
        Object.keys(mealsBySet).forEach(setNum => {
            const title = document.createElement('li');
            title.style.fontWeight = 'bold';
            title.textContent = `الوجبة رقم ${setNum}`;
            previewList.appendChild(title);

            mealsBySet[setNum].forEach(info => {
                const li = document.createElement('li');
                li.textContent = info;
                previewList.appendChild(li);
            });
        });

        document.getElementById('preview').style.display = 'block';
        document.getElementById('previewButton').style.display = 'none';
    } else {
        alert("يرجى اختيار وجبات وإدخال الكمية.");
    }
});

document.getElementById('confirmButton').addEventListener('click', function () {
    document.querySelectorAll('.meal-checkbox:checked').forEach(cb => {
        const container = cb.closest('.meal');
        const input = container.querySelector('input[type="text"]');
        input.disabled = false;
    });

    if (document.querySelectorAll('.meal-checkbox:checked').length > 0) {
        document.getElementById('mealForm').submit();
    } else {
        alert("يرجى اختيار الوجبات قبل التأكيد.");
    }
});

document.getElementById('cancelButton').addEventListener('click', function () {
    document.getElementById('preview').style.display = 'none';
    document
    document.getElementById('preview').style.display = 'none';
    document.getElementById('previewButton').style.display = 'inline';
});
</script>

</body>
</html>
