<?php
include("database.php");
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if inputs are empty
    if (empty($email) || empty($password)) {
        $error = "Email and password cannot be empty.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM Users WHERE email = ?");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            // Store user information in the session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['profile_picture'] = $user['profile_picture'];
            header(header: "Location: index.php"); // Redirect to index.php
            exit();
        } else {
            $error = "Invalid email or password.";
        }
    }
   
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Magic Bites - Login</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
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
<main role="main" id="Login-main">
    <div class="Login-contents">
        <form class="Login-Form" method="post" action="">
            <h2 id="Login-text">Welcome Back</h2>
            <?php if (isset($error)): ?>
                <p style="color:red;"><?php echo $error; ?></p>
            <?php endif; ?>
            <div id="Login-inputs">
                <label for="Email">Email:</label>
                <input type="email" name="email" placeholder="Enter Your Email" required>
            </div>
            <div id="Login-inputs">
                <label for="Password">Password:</label>
                <input type="password" name="password" placeholder="Enter Your Password" required>
            </div>
            <div id="Login-btn"><button type="submit">Login</button></div>
        </form>
        <div id="register">Don't have an account? <a href="Register.php">Create one</a></div>
    </div>
</main>
</body>
</html>