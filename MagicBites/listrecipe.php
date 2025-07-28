<?php
session_start();
require "database.php"; // Include database configuration

$type = isset($_GET['type']) ? $_GET['type'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Initialize the $recipes_result variable to prevent errors
$recipes_result = null;

if (isset($_GET['favorites']) && isset($_SESSION['user_id'])) {
    // Fetch only favorite recipes
    $user_id = $_SESSION['user_id'];
    $recipe_query = "SELECT recipes.* FROM recipes 
                     JOIN favorites ON recipes.recipe_id = favorites.recipe_id 
                     WHERE favorites.user_id = ?";
    $recipe_stmt = $conn->prepare($recipe_query);
    $recipe_stmt->bind_param("i", $user_id);
    $recipe_stmt->execute();
    $recipes_result = $recipe_stmt->get_result();
} else {
    if (!empty($search)) {
        // Fetch recipes where title matches the search term
        $recipe_query = "SELECT * FROM recipes WHERE title LIKE ?";
        $recipe_stmt = $conn->prepare($recipe_query);
        $search_param = "%" . $search . "%"; // Allows partial matches
        $recipe_stmt->bind_param("s", $search_param);
        $recipe_stmt->execute();
        $recipes_result = $recipe_stmt->get_result();
    } elseif (!empty($type) && !empty($category)) {
        // Fetch recipes based on type and category
        $recipe_query = "SELECT * FROM recipes WHERE type = ? AND category = ?";
        $recipe_stmt = $conn->prepare($recipe_query);
        $recipe_stmt->bind_param("ss", $type, $category);
        $recipe_stmt->execute();
        $recipes_result = $recipe_stmt->get_result();
    } else {
        // Fetch all recipes if no filters are applied
        $recipe_query = "SELECT * FROM recipes";
        $recipe_stmt = $conn->prepare($recipe_query);
        $recipe_stmt->execute();
        $recipes_result = $recipe_stmt->get_result();
    }
}
$sql = "SELECT recipe_id FROM recipes" ;
$stmt = $conn->prepare($sql);
$stmt->execute();
$recipe_id= $stmt->$get_result;
$stmt->close();


// Get average rating
$sql = "SELECT AVG(rating) AS avg_rating FROM ratings WHERE recipe_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$avg_rating = $stmt->get_result()->fetch_assoc()['avg_rating'] ?? 0;
$stmt->close();


// insert avg_rating in recipes
$avg_sql= "UPDATE recipes SET avg_rating=( SELECT AVG(rating) AS avg_rating FROM ratings WHERE recipe_id = ?)";
$avg_stmt = $conn->prepare($sql);
$avg_stmt->bind_param("i", $recipe_id);
$avg_stmt->execute();
$avg_stmt->close();



$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipes</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="listrecipe.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/drawer/3.2.2/css/drawer.min.css">
</head>
<body>
    <header>
        <div class="top-bar" style="z-index:5">
            <div class="left-section">
                <img width="50px" height="50px" src="images/logo.png">
                <h1 id="magic" style="margin-left: 15px;">
                    <a href="index.php" style="text-decoration:none;color:white">Magic Bites</a>
                </h1>
            </div>
            <div class="right-section">
            <form action="listrecipe.php" method="GET" class="search-bar">
    <input type="text" name="search" placeholder="Search Recipes">
    <button type="submit">
        <i class="fa-solid fa-magnifying-glass"></i>
    </button>
</form></div>
        </div>
    </header>
    <main class="mainlistrecipe" role="main">
        <h3 class="MyRecipesTitle">Recipes<hr width="200rem" style="height: 2px" color="orange"></h3>
        <div class="recipes-container">
            <?php if ($recipes_result && $recipes_result->num_rows > 0): ?>
                <?php while ($recipe = $recipes_result->fetch_assoc()): ?>
                    <div class="recipe-card">
                        <?php
                        $recipe_image = !empty($recipe['image']) ? "uploads/" . htmlspecialchars($recipe['image']) : "images/default-recipe.jpg";
                        ?>
                        <img style="height:150px" src="<?= $recipe_image ?>" alt="Recipe Image">
                        <h4><?= htmlspecialchars($recipe['title']) ?></h4>
                        <a href="view_recipe.php?id=<?= htmlspecialchars($recipe['recipe_id']) ?>">View Recipe</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No recipes found<?= !empty($search) ? " for '$search'" : " for this category" ?>.</p>
            <?php endif; ?>
        </div>
    </main>
    
</body>
</html>