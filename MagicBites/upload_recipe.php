<?php
session_start();
include("database.php");

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$editing = false;

// Check if we're editing an existing recipe
if (isset($_GET['id'])) {
    $editing = true;
    $recipe_id = intval($_GET['id']);

    // Fetch existing recipe details
    $sql = "SELECT title, description, ingredients, instructions, image, category, type FROM Recipes WHERE recipe_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $recipe_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        die("Recipe not found or access denied.");
    }

    $recipe = $result->fetch_assoc();
} else {
    $recipe = [
        'title' => '',
        'description' => '',
        'ingredients' => '',
        'instructions' => '',
        'image' => '',
        'category' => '',
        'type' => ''
    ];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $ingredients = $_POST['ingredients'];
    $instructions = $_POST['instructions'];
    $image_name = $recipe['image']; // Use existing image by default
    $category = $_POST['category'];
    $type = $_POST['type'];

    // Validate category and type
    $category_types = [
        'MainCourse' => ['Breakfast', 'Lunch', 'Dinner'],
        'Sides' => ['NULL'], 
        'Desserts' => ['Hot', 'Cold', 'Fried'],
        'Drinks' => ['Hot', 'Iced']
    ];

    if (!isset($category_types[$category])) {
        die("Invalid category selected.");
    }

    if (!empty($category_types[$category]) && !in_array($type, $category_types[$category]) && $type !== '') {
        die("Invalid type selected for the chosen category.");
    }

    // Handle image upload (if new image provided)
    if (!empty($_FILES['image']['name'])) {
        $image_name = basename($_FILES['image']['name']);
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($image_name);

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            die("Failed to upload image.");
        }
    }

    if ($editing) {
        // Update existing recipe
        $sql = "UPDATE Recipes 
                SET title = ?, description = ?, ingredients = ?, instructions = ?, image = ?, category = ?, type = ? 
                WHERE recipe_id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssiii", $title, $description, $ingredients, $instructions, $image_name, $category, $type, $recipe_id, $user_id);
    } else {
        // Insert new recipe
        $sql = "INSERT INTO Recipes (title, description, ingredients, instructions, image, category, type, user_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssi", $title, $description, $ingredients, $instructions, $image_name, $category, $type, $user_id);
    }

    if ($stmt->execute()) {
        header("Location: account.php");
        exit;
    } else {
        echo "Error saving recipe: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $editing ? "Edit Recipe" : "Upload Recipe"; ?></title>
    <link rel="stylesheet" href="uploadRecipeStyle.css">
    <link rel="stylesheet" href="style.css">
    <script>
        function updateTypeOptions() {
            const category = document.getElementById('category').value;
            const typeDropdown = document.getElementById('type');
            const options = {
                'MainCourse': ['Breakfast', 'Lunch', 'Dinner'],
                'Sides': ['NULL'],
                'Desserts': ['Hot', 'Cold', 'Fried'],
                'Drinks': ['Hot', 'Iced']
            };

            // Clear previous options
            typeDropdown.innerHTML = '';

            // Add new options based on category
            if (options[category]) {
                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.text = 'None';
                typeDropdown.appendChild(defaultOption);

                options[category].forEach(option => {
                    const opt = document.createElement('option');
                    opt.value = option;
                    opt.text = option;
                    typeDropdown.appendChild(opt);
                });
            } else {
                const opt = document.createElement('option');
                opt.value = '';
                opt.text = 'None';
                typeDropdown.appendChild(opt);
            }
        }
    </script>
</head>
<body style="margin: 0px;">
    <div><header>
        <div class="top-bar">
            <div class="left-section">
                <img width="50px" height="50px" src="images/logo.png">
                <h1 id="magic" style="margin-left: 15px;"><a href="index.php" style="text-decoration:none;color:white">Magic Bites</a></h1>
            </div>
        </div>
    </header></div>
    <main class="MainContent" role="main">
<div class="UploadContent">
    <div class="FormTitle"><h1><?php echo $editing ? "Edit Recipe" : "Upload Recipe"; ?></h1></div>
    <div class="FormContent">
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . ($editing ? "?id=$recipe_id" : ""); ?>" method="POST" enctype="multipart/form-data">
        <label for="title">Recipe Name:</label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($recipe['title']); ?>" required>

        <label for="description">Description:</label>
        <textarea id="description" name="description" rows="4" required><?php echo htmlspecialchars($recipe['description']); ?></textarea>

        <label for="ingredients">Ingredients:</label>
        <textarea id="ingredients" name="ingredients" rows="4"><?php echo htmlspecialchars($recipe['ingredients']); ?></textarea>

        <label for="instructions">Instructions:</label>
        <textarea id="instructions" name="instructions" rows="4"><?php echo htmlspecialchars($recipe['instructions']); ?></textarea>

        <div class="RecipeImage"><label for="image">Recipe Image:</label>
        <?php if ($editing && !empty($recipe['image'])): ?>
            <p>Current Image:</p>
            <img src="uploads/<?php echo htmlspecialchars($recipe['image']); ?>" alt="Recipe Image" style="max-width: 200px;">
        <?php endif; ?>
        <input type="file" id="image" name="image" accept="image/*">
        </div>
        <label for="category">Category:</label>
        <select id="category" name="category" required onchange="updateTypeOptions()">
            <option value="" disabled selected>Select a category</option>
            <option value="MainCourse" <?php echo $recipe['category'] === 'MainCourse' ? 'selected' : ''; ?>>Main Course</option>
            <option value="Sides" <?php echo $recipe['category'] === 'Sides' ? 'selected' : ''; ?>>Sides</option>
            <option value="Desserts" <?php echo $recipe['category'] === 'Desserts' ? 'selected' : ''; ?>>Desserts</option>
            <option value="Drinks" <?php echo $recipe['category'] === 'Drinks' ? 'selected' : ''; ?>>Drinks</option>
        </select>

        <label for="type">Type (Optional):</label>
        <select id="type" name="type"><option value="" selected>None</option></select>

        <div class="SubmitButton"><button type="submit"><?php echo $editing ? "Update Recipe" : "Upload Recipe"; ?></button></div>
        </form>
    </div>
</div>
</main>
</body>
</html>