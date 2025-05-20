<?php
require 'config.php';

if (!isset($_GET['category_id']) || intval($_GET['category_id']) <= 0) {
    echo json_encode([]);
    exit();
}

$categoryId = intval($_GET['category_id']);
$query = "SELECT id, name FROM food_meal WHERE category_id = $1";
$result = pg_query_params($conn, $query, [$categoryId]);

if ($result && pg_num_rows($result) > 0) {
    $foods = [];
    while ($row = pg_fetch_assoc($result)) {
        $foods[] = $row;
    }
    echo json_encode($foods);
} else {
    echo json_encode([]);
}
?>
