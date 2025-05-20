<?php
// Include your database configuration
require 'config.php';
global $conn;
session_start();

// Get the order_id from the URL
$orderId = isset($_GET['order_id']) ? intval($_GET['order_id']) : null;

// Fetch all muscle groups from the database
$muscleGroupQuery = "SELECT * FROM muscle_groups";
$muscleGroupResult = pg_query($conn, $muscleGroupQuery);

if (pg_num_rows($muscleGroupResult) > 0) {
    $muscleGroups = pg_fetch_all($muscleGroupResult);
} else {
    $muscleGroups = [];
}

// If form is submitted, get selected exercises
$selectedExercises = [];
// Ensure this section is inside the if statement that checks the POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['selected_exercises'])) {
        $selectedExercises = $_POST['selected_exercises'];  // Array of selected exercise IDs
        $exerciseGroups = $_POST['exercise_groups'] ?? []; // Keyed by exercise_id

        $userId = $_SESSION['user_id'];  // Assume user ID is stored in session

        if ($orderId) {
            // Loop through selected exercises to fetch names and muscle group names and insert
            foreach ($selectedExercises as $exerciseId) {
                // Get the sets and reps for the current exercise
                $sets = isset($exerciseGroups[$exerciseId]['sets']) ? intval($exerciseGroups[$exerciseId]['sets']) : 1;
                $repsArray = $exerciseGroups[$exerciseId]['reps'] ?? [];

                // Insert each set and rep into the exercise_reps table
                foreach ($repsArray as $setIndex => $repsCount) {
                    $setNumber = $setIndex + 1;
                    $insertRepsQuery = "INSERT INTO exercise_reps (order_id, exercise_id, set_number, reps)
                                        VALUES ($1, $2, $3, $4)";
                    $insertRepsParams = array($orderId, $exerciseId, $setNumber, intval($repsCount));
                    $insertRepsResult = pg_query_params($conn, $insertRepsQuery, $insertRepsParams);

                    if (!$insertRepsResult) {
                        $errorMessage = "خطأ في إدخال التكرارات: " . pg_last_error($conn);
                        echo "<script>alert('$errorMessage');</script>";
                        break 2; // exit both loops
                    }
                }

                // Get the exercise name for the selected exercise
                $getExerciseQuery = "SELECT name FROM exercises WHERE id = $exerciseId";
                $exerciseResult = pg_query($conn, $getExerciseQuery);
                $exerciseData = pg_fetch_assoc($exerciseResult);
                $exerciseName = $exerciseData['name'];

                // Get the muscle group ID from the exercises table
                $getMuscleGroupIdQuery = "SELECT muscle_group_id FROM exercises WHERE id = $exerciseId";
                $muscleGroupIdResult = pg_query($conn, $getMuscleGroupIdQuery);
                $muscleGroupData = pg_fetch_assoc($muscleGroupIdResult);
                $muscleGroupId = $muscleGroupData['muscle_group_id'];

                // Now get the muscle group name based on the muscle_group_id
                $getMuscleGroupNameQuery = "SELECT name FROM muscle_groups WHERE id = $muscleGroupId";
                $muscleGroupNameResult = pg_query($conn, $getMuscleGroupNameQuery);
                $muscleGroupNameData = pg_fetch_assoc($muscleGroupNameResult);
                $muscleGroupName = $muscleGroupNameData['name'];

                // Insert into DB
                $query = "INSERT INTO user_exercises (exercise_id, exercise_name, muscle_group_name, order_id, sets)
                          VALUES ($1, $2, $3, $4, $5)";
                $params = array($exerciseId, $exerciseName, $muscleGroupName, $orderId, $sets);
                $result = pg_query_params($conn, $query, $params);

                if (!$result) {
                    $errorMessage = "حدث خطأ: " . pg_last_error($conn);
                    echo "<script>alert('$errorMessage');</script>";
                    break;
                }
            }

            // After successfully inserting all records, update the order status
            $updateStatusQuery = "UPDATE course_orders SET status = 'done' WHERE order_id = $orderId";
            $updateResult = pg_query($conn, $updateStatusQuery);

            if (!$updateResult) {
                $errorMessage = "فشل في تحديث الحالة: " . pg_last_error($conn);
                echo "<script>alert('$errorMessage');</script>";
            } else {
                echo '<script>alert("تم حفظ التمارين وتحديث الحالة بنجاح!");</script>';
                echo '<meta http-equiv="refresh" content="1;url=captains.php">';
            }
        } else {
            echo "<script>alert('رقم الطلب غير موجود في الرابط.');</script>";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style4.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>تمارين لكل العضلات</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            direction: rtl;
        }

        .menu {
            color: #fff;
            padding: 20px 0;
            margin-bottom: 30px;
        }

        .menu ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
            text-align: center;
        }

        .menu ul li {
            display: inline-block;
            margin: 0 20px;
        }

        .menu ul li a {
            text-decoration: none;
            font-size: 16px;
        }

        .menu ul li.profile {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .menu ul li.profile img {
            border-radius: 50%;
            width: 50px;
            height: 50px;
            margin-right: 10px;
        }

        .content {
            padding: 30px;
            max-width: 1000px;
            margin: 0 auto;
        }

        .title-info {
            text-align: center;
            margin-bottom: 20px;
        }

        .title-info p {
            font-size: 30px;
            font-weight: bold;
            color: #333;
        }

        .title-info i {
            font-size: 35px;
            color: #333;
        }

        .muscle-group {
            background-color: #fff;
            padding: 25px;
            margin-bottom: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .muscle-group h4 {
            font-size: 22px;
            color: #0056b3;
            margin-bottom: 20px;
        }

        .exercise {
            margin-bottom: 15px;
            font-size: 18px;
        }

        .exercise label {
            display: flex;
            align-items: center;
            font-size: 20px;
            color: #555;
        }

        .exercise input[type="checkbox"] {
            margin-left: 15px;
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
            transition: background-color 0.3s;
            margin-top: 30px;
        }

        button[type="submit"]:hover {
            background-color: #003366;
        }

        .selected-exercises {
            margin-top: 30px;
            padding: 25px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .selected-exercises h3 {
            font-size: 26px;
            font-weight: bold;
            color: #333;
        }

        .selected-exercises ul {
            list-style-type: none;
            padding: 0;
        }

        .selected-exercises ul li {
            font-size: 20px;
            color: #444;
            margin-bottom: 15px;
        }
    </style>
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

        <li>
            <a href="captains.php">
                <i class="fas fa-home"></i>
                <p>Dashboard</p>
            </a>
        </li>

        <li>
            <a href="display_members.php">
                <i class="fas fa-user-group"></i>
                <p>المتدربين</p>
            </a>
        </li>

        <li>
            <a href="display_products.php">
                <i class="fas fa-table"></i>
                <p>المنتجات</p>
            </a>
        </li>

        <li>
            <a href="#">
                <i class="fas fa-table"></i>
                <p>طلبات المتدربين</p>
            </a>
        </li>

        <li class="log-out">
            <a href="logout.php">
                <i class="fas fa-sign-out"></i>
                <p>تسجيل خروج</p>
            </a>
        </li>
    </ul>
</div>


<div class="content">
    <div class="title-info">
        <p>تمارين لكل العضلات</p>
        <i class="fas fa-dumbbell"></i>
    </div>

    <form action="" method="POST">
        <h3>اختر التمارين:</h3>

        <?php foreach ($muscleGroups as $group): ?>
            <div class="muscle-group">
                <h4><?php echo htmlspecialchars($group['name']); ?></h4>

                <?php
                $exerciseQuery = "SELECT * FROM exercises WHERE muscle_group_id = " . $group['id'];
                $exerciseResult = pg_query($conn, $exerciseQuery);

                if (pg_num_rows($exerciseResult) > 0):
                    while ($exercise = pg_fetch_assoc($exerciseResult)):
                        ?>
                        <div class="exercise">
                        <label>
                        <input type="checkbox" name="selected_exercises[]"
       value="<?php echo $exercise['id']; ?>"
       <?php echo in_array($exercise['id'], $selectedExercises) ? 'checked' : ''; ?>>
<?php echo htmlspecialchars($exercise['name']); ?>
<label for="sets_<?php echo $exercise['id']; ?>">عدد المجاميع:</label>
<select id="sets_<?php echo $exercise['id']; ?>" 
        name="exercise_groups[<?php echo $exercise['id']; ?>][sets]" 
        onchange="updateReps(this, '<?php echo $exercise['id']; ?>')">
    <option value="">اختر عدد المجاميع</option>
    <?php for ($i = 1; $i <= 4; $i++): ?>
        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
    <?php endfor; ?>
</select>







                        </div>
                    <?php
                    endwhile;
                else:
                    echo "<p>لا توجد تمارين لهذا العضلة.</p>";
                endif;
                ?>

            </div>
        <?php endforeach; ?>

        <button type="submit">حفظ التمارين المختارة</button>
    </form>
</div>

</body>
</html>
