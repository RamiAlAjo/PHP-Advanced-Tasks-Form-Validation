<?php
session_start();
include 'includes/db.php';
include 'includes/functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = validate_input($_POST["email"]);
    $password = validate_input($_POST["password"]);

    if (!validate_email($email)) {
        echo "Invalid email format.";
    } else {
        $hashed_password = hash('sha256', $password);

        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
            $stmt->execute([$email, $hashed_password]);
            $user = $stmt->fetch();

            if ($user) {
                $_SESSION['user'] = $user;
                if ($user['role'] == 'admin') {
                    header('Location: admin.php');
                } else {
                    header('Location: welcome.php');
                }
                exit();
            } else {
                echo "Invalid email or password.";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/styles.css">
    <script>
        function validateForm() {
            let email = document.forms["loginForm"]["email"].value;
            let password = document.forms["loginForm"]["password"].value;

            let emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;

            if (!emailPattern.test(email)) {
                alert("Invalid email format.");
                return false;
            }
            if (password === "") {
                alert("Password is required.");
                return false;
            }
            return true;
        }
    </script>
</head>

<body>
    <form name="loginForm" action="login.php" method="post" onsubmit="return validateForm()">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <input type="submit" value="Login">
    </form>
</body>

</html>