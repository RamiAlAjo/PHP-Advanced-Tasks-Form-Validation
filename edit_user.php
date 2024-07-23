<?php
session_start();
include 'includes/db.php';
include 'includes/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$errors = [];
$user = null;

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header("Location: admin_dashboard.php");
        exit();
    }
} else {
    header("Location: admin_dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = validate_input($_POST['first_name']);
    $last_name = validate_input($_POST['last_name']);
    $email = validate_input($_POST['email']);
    $mobile = validate_input($_POST['mobile']);
    $password = validate_input($_POST['password']);
    $confirm_password = validate_input($_POST['confirm_password']);
    $image = $_FILES['image'];

    if (empty($first_name) || empty($last_name) || empty($email) || empty($mobile)) {
        $errors[] = "All fields are required.";
    }

    if (!validate_email($email)) {
        $errors[] = "Invalid email format.";
    }

    if (!validate_mobile($mobile)) {
        $errors[] = "Mobile number must be 10 digits.";
    }

    if ($password && (!validate_password($password) || $password !== $confirm_password)) {
        $errors[] = "Password must be at least 8 characters long, include uppercase, lowercase, numbers, and special characters, and passwords must match.";
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
        $update_query = "UPDATE users SET first_name = ?, last_name = ?, email = ?, mobile = ?";
        $params = [$first_name, $last_name, $email, $mobile];

        if ($password) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $update_query .= ", password = ?";
            $params[] = $hashed_password;
        }

        if ($imageData) {
            $update_query .= ", imageType = ?, imageData = ?";
            $params[] = $imageType;
            $params[] = $imageData;
        }

        $update_query .= " WHERE id = ?";
        $params[] = $user_id;

        $stmt = $pdo->prepare($update_query);
        $stmt->execute($params);

        header("Location: admin_dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h2>Edit User</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=" . htmlspecialchars($user_id); ?>" enctype="multipart/form-data">
        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>

        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

        <label for="mobile">Mobile:</label>
        <input type="text" id="mobile" name="mobile" value="<?php echo htmlspecialchars($user['mobile']); ?>" required>

        <label for="password">New Password:</label>
        <input type="password" id="password" name="password">

        <label for="confirm_password">Confirm New Password:</label>
        <input type="password" id="confirm_password" name="confirm_password">

        <label for="image">Profile Image:</label>
        <input type="file" id="image" name="image" accept="image/*">

        <input type="submit" value="Update">
    </form>
    <button onclick="window.location.href='admin_dashboard.php'">Back to Dashboard</button>

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
