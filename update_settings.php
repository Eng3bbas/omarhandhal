<?php
require 'config.php';
session_start();

// التأكد من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('الرجاء تسجيل الدخول أولاً.'); window.location.href = 'login.php';</script>";
    exit;
}

$userId = (int)$_SESSION['user_id'];

if (isset($_POST['update_info'])) {
    // الحصول على البيانات من النموذج
    $userAge = (int)$_POST['user_age'];
    $userLength = (int)$_POST['user_length'];
    $userWeight = (int)$_POST['user_weight'];
    $userStatus = pg_escape_string($_POST['user_status']);
    $userChild = (int)$_POST['user_child'];
    $gender = pg_escape_string($_POST['gender']);
    $activityLevel = pg_escape_string($_POST['activity_level']);

    // استعلام التحديث
    $updateQuery = "
        UPDATE members
        SET user_age = $userAge,
            user_length = $userLength,
            user_weight = $userWeight,
            user_status = '$userStatus',
            user_child = $userChild,
            gender = '$gender',
            activity_level = '$activityLevel'
        WHERE user_id = $userId
    ";

    $updateResult = pg_query($conn, $updateQuery);

    if ($updateResult) {
        echo "<script>alert('تم تحديث البيانات بنجاح.'); window.location.href = 'settings.php';</script>";
    } else {
        echo "<script>alert('فشل في تحديث البيانات.'); window.location.href = 'settings.php';</script>";
    }
}
?>
