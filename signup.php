<?php
include 'includes/db.php';
include 'includes/functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = validate_input($_POST["email"]);
    $mobile = validate_input($_POST["mobile"]);
    $first_name = validate_input($_POST["first_name"]);
    $middle_name = validate_input($_POST["middle_name"]);
    $last_name = validate_input($_POST["last_name"]);
    $family_name = validate_input($_POST["family_name"]);
    $password = validate_input($_POST["password"]);
    $confirm_password = validate_input($_POST["confirm_password"]);

    $errors = [];
    if (!validate_email($email)) {
        $errors[] = "Invalid email format.";
    }
    if (!validate_mobile($mobile)) {
        $errors[] = "Invalid mobile number.";
    }
    if (empty($first_name) || empty($middle_name) || empty($last_name) || empty($family_name)) {
        $errors[] = "All name fields are required.";
    }
    if (!validate_password($password)) {
        $errors[] = "Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character.";
    }
    if (!password_match($password, $confirm_password)) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        $hashed_password = hash('sha256', $password);

        try {
            $stmt = $pdo->prepare("INSERT INTO users (email, mobile, first_name, middle_name, last_name, family_name, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$email, $mobile, $first_name, $middle_name, $last_name, $family_name, $hashed_password]);
            echo "Registration successful!";
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                echo "Email already exists.";
            } else {
                echo "Error: " . $e->getMessage();
            }
        }
    } else {
        foreach ($errors as $error) {
            echo "<p>$error</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="css/styles.css">
    <script>
        function validateForm() {
            let email = document.forms["signupForm"]["email"].value;
            let mobile = document.forms["signupForm"]["mobile"].value;
            let first_name = document.forms["signupForm"]["first_name"].value;
            let middle_name = document.forms["signupForm"]["middle_name"].value;
            let last_name = document.forms["signupForm"]["last_name"].value;
            let family_name = document.forms["signupForm"]["family_name"].value;
            let password = document.forms["signupForm"]["password"].value;
            let confirm_password = document.forms["signupForm"]["confirm_password"].value;

            let emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
            let mobilePattern = /^\d{10}$/;
            let passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

            if (!emailPattern.test(email)) {
                alert("Invalid email format.");
                return false;
            }
            if (!mobilePattern.test(mobile)) {
                alert("Invalid mobile number.");
                return false;
            }
            if (first_name === "" || middle_name === "" || last_name === "" || family_name === "") {
                alert("All name fields are required.");
                return false;
            }
            if (!passwordPattern.test(password)) {
                alert("Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character.");
                return false;
            }
            if (password !== confirm_password) {
                alert("Passwords do not match.");
                return false;
            }
            return true;
        }
    </script>
</head>

<body>
    <form name="signupForm" action="signup.php" method="post" onsubmit="return validateForm()">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <label for="mobile">Mobile:</label>
        <input type="text" id="mobile" name="mobile" required>
        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" required>
        <label for="middle_name">Middle Name:</label>
        <input type="text" id="middle_name" name="middle_name" required>
        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name" required>
        <label for="family_name">Family Name:</label>
        <input type="text" id="family_name" name="family_name" required>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
        <input type="submit" value="Sign Up">
    </form>
</body>

</html>