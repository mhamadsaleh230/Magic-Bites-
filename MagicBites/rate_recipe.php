<?php
session_start();
require "database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['recipe_id'], $_POST['rating'])) {
    $recipe_id = intval($_POST['recipe_id']);
    $user_id = $_SESSION['user_id'] ?? null;
    $rating = intval($_POST['rating']);

    if (!$user_id) {
        echo json_encode(["success" => false, "message" => "User not logged in."]);
        exit;
    }

    // Check if user already rated this recipe
    $check_sql = "SELECT * FROM ratings WHERE recipe_id = ? AND user_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $recipe_id, $user_id);
    $check_stmt->execute();
    $existing_rating = $check_stmt->get_result()->fetch_assoc();
    $check_stmt->close();

    if ($existing_rating) {
        $update_sql = "UPDATE ratings SET rating = ? WHERE recipe_id = ? AND user_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("iii", $rating, $recipe_id, $user_id);
        $update_stmt->execute();
        $update_stmt->close();
    } else {
        $insert_sql = "INSERT INTO ratings (recipe_id, user_id, rating) VALUES (?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("iii", $recipe_id, $user_id, $rating);
        $insert_stmt->execute();
        $insert_stmt->close();
    }

    // Get updated average rating
    $sql = "SELECT AVG(rating) AS avg_rating FROM ratings WHERE recipe_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $recipe_id);
    $stmt->execute();
    $new_avg = $stmt->get_result()->fetch_assoc()['avg_rating'] ?? 0;
    $stmt->close();

    

    echo json_encode(["success" => true, "new_avg" => number_format($new_avg, 1)]);
}

$conn->close();
?>