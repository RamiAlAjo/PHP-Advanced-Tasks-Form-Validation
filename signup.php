<?php
session_start();
include 'includes/db.php';
include 'includes/functions.php';

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = validate_input($_POST['first_name']);
    $last_name = validate_input($_POST['last_name']);
    $email = validate_input($_POST['email']);
    $mobile = validate_input($_POST['mobile']);
    $password = validate_input($_POST['password']);
    $confirm_password = validate_input($_POST['confirm_password']);
    $role = validate_input($_POST['role']);
    $image = $_FILES['image'];

    if (empty($first_name) || empty($last_name) || empty($email) || empty($mobile) || empty($password) || empty($confirm_password) || empty($role)) {
        $errors[] = "All fields are required.";
    }

    if (!validate_email($email)) {
        $errors[] = "Invalid email format.";
    }

    if (!validate_mobile($mobile)) {
        $errors[] = "Mobile number must be 10 digits.";
    }

    if (!validate_password($password)) {
        $errors[] = "Password must be at least 8 characters long and include uppercase, lowercase, numbers, and special characters.";
    }

    if (!password_match($password, $confirm_password)) {
        $errors[] = "Passwords do not match.";
    }

    if ($image['error'] == UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($image['type'], $allowed_types)) {
            $errors[] = "Invalid image type. Only JPG, PNG, and GIF are allowed.";
        } else {
            $imageData = file_get_contents($image['tmp_name']);
            $imageType = $image['type'];
        }
    } else {
        $imageData = null;
        $imageType = null;
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, mobile, password, role, imageType, imageData) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$first_name, $last_name, $email, $mobile, $hashed_password, $role, $imageType, $imageData]);

        header("Location: login.php");
        exit();
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
    <script src="js/validation.js"></script>
</head>
<body>
    <h2>Sign Up</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data" onsubmit="return validateSignupForm()">
        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" required>

        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="mobile">Mobile:</label>
        <input type="text" id="mobile" name="mobile" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>

        <label for="role">Role:</label>
        <select id="role" name="role" required>
            <option value="">Select Role</option>
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select>

        <label for="image">Profile Image:</label>
        <input type="file" id="image" name="image" accept="image/*">

        <input type="submit" value="Sign Up">
    </form>
    <button onclick="window.location.href='index.php'">Home</button>

    <?php
    if (!empty($errors)) {
        echo '<ul>';
        foreach ($errors as $error) {
            echo '<li>' . htmlspecialchars($error) . '</li>';
        }
        echo '</ul>';
    }
    ?>
</body>
</html>
