<?php
require_once 'includes/db.php';

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    $stmt = $pdo->prepare("SELECT imageType, imageData FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        header("Content-type: " . htmlspecialchars($row["imageType"]));
        echo $row["imageData"];
    } else {
        echo "No image found.";
    }
} else {
    echo "Invalid request.";
}
?>
