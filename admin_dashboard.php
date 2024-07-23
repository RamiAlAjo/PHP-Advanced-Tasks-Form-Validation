<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM users");
$stmt->execute();
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h1>Admin Dashboard</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>User Image</th>
                <th>Name</th>
                <th>Email</th>
                <th>Date Created</th>
                <th>Phone Number</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo htmlspecialchars($user['id']); ?></td>
                <td>
                    <?php if ($user['imageData']): ?>
                        <img src="display_image.php?user_id=<?php echo htmlspecialchars($user['id']); ?>" alt="User Image" style="width: 100px; height: auto;">
                    <?php else: ?>
                        No image
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo htmlspecialchars($user['date_created']); ?></td>
                <td><?php echo htmlspecialchars($user['mobile']); ?></td>
                <td>
                    <a href="edit_user.php?id=<?php echo htmlspecialchars($user['id']); ?>">Edit</a>
                    <a href="delete_user.php?id=<?php echo htmlspecialchars($user['id']); ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p><a href="logout.php">Logout</a></p>
</body>
</html>
