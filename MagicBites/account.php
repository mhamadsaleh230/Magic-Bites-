<?php
session_start();
require "database.php"; // Include database configuration

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$query = "SELECT * FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();
$profile_image = $user['profile_picture'] ?? "uploads/default_profile.jpg"; // Default profile picture
// Handle profile image upload
$error_message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_image'])) {
    // Directory for storing images
    $target_dir = "uploads/";

    // Ensure uploads directory exists
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true); // Create directory with write permissions
    }

    $file_name = basename($_FILES['profile_image']['name']);
    $target_file = $target_dir . $file_name;

    // Allowed file types
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($file_type, $allowed_types)) {
        // Move the file to the uploads directory
        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
            // Update user's profile picture in the database
            $update_query = "UPDATE users SET profile_picture = ? WHERE user_id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("si", $target_file, $user_id);

            if ($update_stmt->execute()) {
                $profile_image = $target_file; // Update the displayed profile image
            } else {
                $error_message = "Failed to update profile picture in the database.";
            }
        } else {
            $error_message = "Failed to upload the file.";
        }
    } else {
        $error_message = "Only JPG, JPEG, PNG, and GIF files are allowed.";
    }
}

// Fetch user's recipes
$recipe_query = "SELECT * FROM recipes WHERE user_id = ?";
$recipe_stmt = $conn->prepare($recipe_query);
$recipe_stmt->bind_param("i", $user_id);
$recipe_stmt->execute();
$recipes_result = $recipe_stmt->get_result();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deletebutton'])) {
    if (isset($_POST["recipe_id"])) {
        $idpost = $_POST["recipe_id"];

        // Start by deleting from the related tables:
        
        // 1. Delete related comments
        $delete_comments_query = "DELETE FROM comments WHERE recipe_id = ?";
        $delete_comments_stmt = $conn->prepare($delete_comments_query);

        if ($delete_comments_stmt) {
            $delete_comments_stmt->bind_param("i", $idpost);
            $delete_comments_stmt->execute();
            $delete_comments_stmt->close();
            
        } 
        // 2. Delete related ratings
        $delete_ratings_query = "DELETE FROM ratings WHERE recipe_id = ?";
        $delete_ratings_stmt = $conn->prepare($delete_ratings_query);

        if ($delete_ratings_stmt) {
            $delete_ratings_stmt->bind_param("i", $idpost);
            $delete_ratings_stmt->execute();
            $delete_ratings_stmt->close();
        } 

        // 3. Delete from favorites table
        $delete_favorites_query = "DELETE FROM favorites WHERE recipe_id = ?";
        $delete_favorites_stmt = $conn->prepare($delete_favorites_query);

        if ($delete_favorites_stmt) {
            $delete_favorites_stmt->bind_param("i", $idpost);
            $delete_favorites_stmt->execute();
            $delete_favorites_stmt->close();
        }

        // 4. Finally, delete the recipe
        $delete_recipe_query = "DELETE FROM recipes WHERE recipe_id = ?";
        $delete_recipe_stmt = $conn->prepare($delete_recipe_query);

        if ($delete_recipe_stmt) {
            $delete_recipe_stmt->bind_param("i", $idpost);
            $delete_recipe_stmt->execute();
            $delete_recipe_stmt->close();
            header(header: "Location: account.php");?>
            <a href="#MyRecipesTitles"></a>";
        
<?PHP }}}?>
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account</title>
    <link rel="stylesheet" href="accountPageStyle.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <style>
        .recipes-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    padding: 20px;
    background-image: url(images/SideHover.png);
    background-repeat: no-repeat;
    background-size: cover;
}

.recipe-card {
    width: 200px;
    border: 1px solid #ddd;
    border-radius: 10px;
    overflow: hidden;
    background-image: url(images/sideBackground2.jpg);
    background-repeat: no-repeat;
    background-size: cover;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.recipe-card img {
    width: 100%;
    height: auto;
}

.recipe-card h4 {
    font-size: 18px;
    margin: 15px;
    text-align: center;
}

.recipe-card a {
    display: block;
    text-align: center;
    margin: 10px;
    padding: 10px;
    background-color: orange;
    color: white;
    text-decoration: none;
    border-radius: 5px;
}

.recipe-card form input {
    display: block;
    text-align: center;
    margin: 10px;
    padding: 10px;
    background-color: orange;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    border: none;
}

.recipe-card:hover {
    transform: scale(1.05);
}
    </style>
    <header>
        <div class="top-bar">
            <div class="left-section">
                <img width="50px" height="50px" src="images/logo.png">
                <h1 id="magic" style="margin-left: 15px;"><a href="index.php" style="text-decoration:none;color:white">Magic Bites</a></h1>
            </div>
        </div>
    </header>
    <main role="main">
        <div class="profile-container">
            <img src="<?= htmlspecialchars($profile_image) ?>" alt="Profile Image" class="profile-image">
            <h2><?= htmlspecialchars($user['username']) ?></h2>
            <p>Email: <?= htmlspecialchars($user['email']) ?></p>

            <div class="nav-links">
            <a href="listRecipe.php?favorites=1">Favorite Recipes</a>
                <a href="upload_recipe.php">Upload Recipe</a>
                <a href="logout.php">Logout</a>
            </div>
            <div class="ChangeImageArea">
                <form action="account.php" method="POST" enctype="multipart/form-data">
                    <h3 class="updatetitle" style="color:black">Update Profile Picture <hr class="updateline" color="orange"></h3>
                    <input class="ChooseInput" type="file" name="profile_image" required>
                    <button class="ChangeButton" type="submit">Change</button>
                </form>
            </div>
            <?php if ($error_message): ?>
                <p class="error-message"><?= htmlspecialchars($error_message) ?></p>
            <?php endif; ?>
        </div>
        <h3 id="MyRecipesTitle">My Recipes<hr width="200rem" style="height: 2px" color="orange" ></h3>
        <div class="recipes-container">
            <?php while ($recipe = $recipes_result->fetch_assoc()): ?>
                <div class="recipe-card">
                    <?php
$recipe_image = !empty($recipe['image']) ? "uploads/" . htmlspecialchars($recipe['image']) : "images/default-recipe.jpg";
?>
<img src="<?= $recipe_image ?>" style="height:150px" alt="Recipe Image">
                    <h4><?= htmlspecialchars($recipe['title']) ?></h4>
                    <a href="view_recipe.php?id=<?= htmlspecialchars($recipe['recipe_id']) ?>">View Recipe</a>
                    <form action="account.php" method="POST">
                    <input type="hidden" value="<?= htmlspecialchars($recipe['recipe_id']) ?>" name="recipe_id" style="background-color:red">
                    <input type="submit" value="Delete Recipe" style="background-color:red" name="deletebutton">
                    </form>
            </div>
            <?php endwhile; ?>
        </div>
    </main>
</body>
</html>
