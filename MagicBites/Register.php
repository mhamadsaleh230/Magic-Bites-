<?php 
include("database.php"); 
session_start();

if (!$conn) {
    die("Database connection not established."); // Debugging line
}

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error_message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error_message = "This email is already registered.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hashed_password);

            if ($stmt->execute()) {
                $success_message = "Registration successfull! You can now log in.";
            } else {
                $error_message = "Something went wrong. Please try again later.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Magic Bites - Register</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
</head>
<body id="LoginBody">
<header>
   <div class="top-bar">
        <div class="left-section">
            <img width="50px" height="50px" src="images/logo.png">
            <h1 id="magic" style="margin-left: 15px;"><a href="index.php" style="text-decoration:none;color:white">Magic Bites</a></h1>
        </div>
    </div>
</header>
<main role="main" id="Register-main">
    <div class="Login-contents">
        <form class="Login-Form" method="POST" action="">
            <h2 id="Login-text">Welcome</h2>
            <?php if (!empty($error_message)): ?>
                <p style="color: red;"><?= htmlspecialchars($error_message) ?></p>
            <?php elseif (!empty($success_message)): ?>
                <p style="color: green;"><?= htmlspecialchars($success_message) ?></p>
            <?php endif; ?>
            <div id="Login-inputs">
                <label for="username">Username:</label>
                <input type="text" name="username" placeholder="Enter a Username" required>
            </div>
            <div id="Login-inputs">
                <label for="email">Email:</label>
                <input type="email" name="email" placeholder="Enter Your Email" required>
            </div>
            <div id="Login-inputs">
                <label for="password">Password:</label>
                <input type="password" name="password" placeholder="Enter Your Password" required>
            </div>
            <div id="Login-inputs">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" name="confirm_password" placeholder="Confirm Your Password" required>
            </div>
            <div id="Login-btn">
                <button type="submit">Register</button>
            </div>
        </form>
    </div>
</main>
</body>
</html>