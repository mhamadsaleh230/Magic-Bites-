<?php
session_start();
require 'database.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['recipe_id'])) {
    $recipe_id = intval($_POST['recipe_id']);
    
    // Assuming the user is logged in and has a user_id stored in SESSION
    if (!isset($_SESSION['user_id'])) {
        echo "You must be logged in to add favorites.";
        exit;
    }

    $user_id = $_SESSION['user_id'];
    // Check if the recipe is already in favorites
    $check_sql = "SELECT 1 FROM Favorites WHERE user_id = ? AND recipe_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $user_id, $recipe_id);
    $check_stmt->execute();
    $already_favorited = $check_stmt->get_result()->num_rows > 0;
    $check_stmt->close();

    // If not already favorited, insert into the database
    if (!$already_favorited) {
        // Prepare the SQL query to insert into Favorites table
        $stmt = $conn->prepare("INSERT INTO Favorites (user_id, recipe_id) VALUES (?, ?)");
        if ($stmt) {
            $stmt->bind_param("ii", $user_id, $recipe_id);
            if ($stmt->execute()) {
                echo "Recipe added to favorites!";
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        }
    } else {
        echo "This recipe is already in your favorites.";
    }
}

$user_id = $_SESSION['user_id'];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid Recipe ID.");
}

$recipe_id = intval($_GET['id']);

// Fetch recipe details
$sql = "SELECT Recipes.recipe_id, title, description, ingredients, instructions, image, Users.username, creation_date 
        FROM Recipes 
        JOIN Users ON Recipes.user_id = Users.user_id
        WHERE recipe_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) die("Recipe not found.");
$recipe = $result->fetch_assoc();
$stmt->close();

// Check if the user has already favorited this recipe
$sql = "SELECT 1 FROM Favorites WHERE user_id = ? AND recipe_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $recipe_id);
$stmt->execute();
$is_favorited = $stmt->get_result()->num_rows > 0;
$stmt->close();

// Get user's rating (if any)
$sql = "SELECT rating FROM ratings WHERE user_id = ? AND recipe_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $recipe_id);
$stmt->execute();
$user_rating = $stmt->get_result()->fetch_assoc()['rating'] ?? 0;
$stmt->close();

// Get average rating
$sql = "SELECT AVG(rating) AS avg_rating FROM ratings WHERE recipe_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$avg_rating = $stmt->get_result()->fetch_assoc()['avg_rating'];
if($avg_rating=== null){
    $avg_rating= 0.0;
}
$stmt->close();


// insert avg_rating in recipes
$avg_sql= "UPDATE recipes SET avg_rating= ?  WHERE recipe_id= ? ";
$avg_stmt = $conn->prepare($avg_sql);
$avg_stmt->bind_param("di",  $avg_rating, $recipe_id);
$avg_stmt->execute();
$avg_stmt->close();

// Get updated average rating
$sql = "SELECT AVG(rating) AS avg_rating FROM ratings WHERE recipe_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$new_avg = $stmt->get_result()->fetch_assoc()['avg_rating'] ?? 0;
$stmt->close();



echo json_encode(["success" => true, "new_avg" => number_format($new_avg, 1)]);


// Fetch comments
$sql = "SELECT Comments.comment_text, Comments.comment_date, Users.username 
        FROM Comments 
        JOIN Users ON Comments.user_id = Users.user_id 
        WHERE Comments.recipe_id = ? 
        ORDER BY Comments.comment_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$comments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Handle comment submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['comment_text'])) {
    $comment_text = trim($_POST['comment_text']);

    if (!empty($comment_text)) {
        $sql = "INSERT INTO Comments (recipe_id, user_id, comment_text) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("iis", $recipe_id, $user_id, $comment_text);
            if ($stmt->execute()) {
                header("Location: view_recipe.php?id=$recipe_id"); // Refresh to show new comment
                exit;
            }
            $stmt->close();
        }
    }
}
$conn->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($recipe['title']) ?></title>
    <link rel="stylesheet" href="styles.css"> 
    <script src="https://kit.fontawesome.com/YOUR-FONT-AWESOME-KIT.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="view-recipe.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/drawer/3.2.2/css/drawer.min.css">
    <style>
        .star { font-size: 30px; cursor: pointer; color: gray; transition: color 0.2s; }
        .star.selected, .star:hover { color: gold; }
        .comment-section { margin-top: 20px; }
        .comment-box { width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ccc; }
        .comment { background: #f9f9f9; padding: 10px; margin-bottom: 5px; border-left: 3px solid #007BFF; }
        .comment strong { color: #007BFF; }
    </style>
</head>
<body>
<header>
        <div class="top-bar">
            <div class="left-section">
                <img width="50px" height="50px" src="images/logo.png">
                <h1 id="magic" style="margin-left: 15px;"><a href="index.php" style="text-decoration:none;color:white">Magic Bites</a></h1>
            </div>
        </div>
    </header>
<main class="mainrecipes" role="main">
    <h1 class="recipetitle"><?= htmlspecialchars($recipe['title']) ?></h1>
    <div class="recipe-container">
        <?php if (!empty($recipe['image'])): ?>
            <div class="recipe-img">
                <img style="border-radius:20px" height="350rem" width="350rem" src="uploads/<?= htmlspecialchars($recipe['image']) ?>" alt="Recipe Image" style="max-width: 300px;">
            </div>
        <?php endif; ?>
        
       <div class="content"> 
        <p ><div style="font-size:1.8rem"> <strong>Description:</strong></div> <div style="color:black;font-size:1.5rem"><?= nl2br(htmlspecialchars($recipe['description'])) ?></div></p>
        <p><div style="float:left;font-size:1.8rem"><strong>Ingredients:</strong> </div><div style="color:black;font-size:1.5rem"><?= nl2br(htmlspecialchars($recipe['ingredients'])) ?></div></p>
        <p><div style="float:left;font-size:1.8rem"><strong>Instructions:</strong> </div> <div style="color:black;font-size:1.5rem"><?= nl2br(htmlspecialchars($recipe['instructions'])) ?></div></p>
        <p><div style="float:left;font-size:1.8rem"><strong>Created by:</strong> </div><div style="color:black;font-size:1.5rem"><?= htmlspecialchars($recipe['username']) ?></div></p>
        <p><div style="float:left;font-size:1.8rem"><strong>Creation Date:</strong> </div><div style="color:black;font-size:1.5rem"><?= htmlspecialchars($recipe['creation_date']) ?></div></p>

        <!-- ⭐ Star Rating System -->
        <h3>Rate this Recipe</h3>
        <div id="rating-stars">
            <i class="fa-solid fa-star star" data-rating="1"></i>
            <i class="fa-solid fa-star star" data-rating="2"></i>
            <i class="fa-solid fa-star star" data-rating="3"></i>
            <i class="fa-solid fa-star star" data-rating="4"></i>
            <i class="fa-solid fa-star star" data-rating="5"></i>
        </div>
        <p>Average Rating: <span id="average-rating"><?= number_format($avg_rating, 1) ?></span> / 5</p>
</div>

<!-- Add to Favorites Button -->
<form method="POST">
    <input type="hidden" name="recipe_id" value="<?= htmlspecialchars($recipe_id) ?>">
    <button id="favButton" type="submit">Add to Favorites</button>
</form>
<span id="favMessage"></span> <!-- To display success/error messages -->

        <a id="backtorecipes" href="listrecipe.php">Back to Recipes</a>
    </div>
    
    <!-- 📝 Comment Section -->
    <div class="comment-section">
                <!-- Comment Form -->

        <form method="POST">
            <textarea name="comment_text" class="comment-box" placeholder="Write a comment..." required></textarea>
            <button type="submit">Post Comment</button>
        </form>
        <h3>Comments</h3>
        
        <!-- Show existing comments -->
        <?php foreach ($comments as $comment): ?>
            <div class="comment">
                <strong><?= htmlspecialchars($comment['username']) ?>:</strong>
                <p><?= nl2br(htmlspecialchars($comment['comment_text'])) ?></p>
                <small><?= $comment['comment_date'] ?></small>
            </div>
        <?php endforeach; ?>

        
    </div>
</main>
    <script>
    $(document).ready(function () {
        $("#favButton").click(function (e) {
            e.preventDefault();

            $.post("view_recipe.php", { recipe_id: <?= $recipe_id ?> }, function(response) {
                $("#favMessage").text(response); // Display success or error message
            });
        });
    });
    </script>
    <script>
    $(document).ready(function () {
        $(".star").click(function () {
            let rating = $(this).data("rating");

            $.post("rate_recipe.php", { recipe_id: <?= $recipe_id ?>, rating: rating }, function (response) {
                if (response.success) {
                    $("#average-rating").text(response.new_avg);
                    $(".star").removeClass("selected").css("color", "gray");
                    $(".star").each(function () {
                        if ($(this).data("rating") <= rating) {
                            $(this).css("color", "gold");
                        }
                    });
                } else {
                    alert("Error: " + response.error);
                }
            }, "json");
        });
    });
    </script>
</body>
</html>