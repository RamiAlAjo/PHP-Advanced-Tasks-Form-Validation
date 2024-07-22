<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header('Location: login.php');
    exit();
}

$users = $pdo->query("SELECT * FROM users")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <h1>Admin Dashboard</h1>
    <table>
        <tr>
            <th>ID</th>
            <th>User Image</th>
            <th>Name</th>
            <th>Email</th>
            <th>Date Created</th>
            <th>Phone Number</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($users as $user) : ?>
            <tr>
                <td><?php echo $user['id']; ?></td>
                <td><img src="images/<?php echo $user['image']; ?>" alt="User Image"></td>
                <td><?php echo $user['first_name'] . ' ' . $user['middle_name'] . ' ' . $user['last_name'] . ' ' . $user['family_name']; ?></td>
                <td><?php echo $user['email']; ?></td>
                <td><?php echo $user['date_created']; ?></td>
                <td><?php echo $user['mobile']; ?></td>
                <td>
                    <a href="edit_user.php?id=<?php echo $user['id']; ?>">Edit</a>
                    <a href="delete_user.php?id=<?php echo $user['id']; ?>">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>

</html>