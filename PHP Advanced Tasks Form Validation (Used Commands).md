# PHP Advanced Tasks Form Validation (Used Commands)

## Step 1. Create Database

```sql
CREATE DATABASE php_form_validation;

USE php_form_validation;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    mobile VARCHAR(10) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    middle_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    family_name VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (email, mobile, first_name, middle_name, last_name, family_name, password, role)
VALUES ('admin@example.com', '1234567890', 'Admin', 'Super', 'User', 'Admin', SHA2('Admin@123', 256), 'admin');
```

---

## Step 2. Configuration (db.php)

```php
<?php
$host = 'localhost';
$db = 'php_form_validation';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database $db :" . $e->getMessage());
}
?>
```

---

## Step 3. Validation Functions (functions.php)

```php
<?php
function validate_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validate_mobile($mobile) {
    return preg_match('/^\d{10}$/', $mobile);
}

function validate_password($password) {
    return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password);
}

function password_match($password, $confirm_password) {
    return $password === $confirm_password;
}
?>
```

---

## Step 4. (Optional) Styling (styles.css)

```css
body {
  font-family: Arial, sans-serif;
  background-color: #f4f4f4;
  margin: 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
}

h1 {
  color: #333;
}

form {
  background: #fff;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  width: 300px;
  margin-top: 20px;
}

label {
  display: block;
  margin-bottom: 5px;
  color: #333;
}

input[type="text"],
input[type="email"],
input[type="password"] {
  width: 100%;
  padding: 8px;
  margin-bottom: 10px;
  border: 1px solid #ccc;
  border-radius: 4px;
}

input[type="submit"] {
  background-color: #28a745;
  color: white;
  padding: 10px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

input[type="submit"]:hover {
  background-color: #218838;
}

a {
  text-decoration: none;
  color: #007bff;
}

a:hover {
  text-decoration: underline;
}

p {
  color: red;
}
```

---

## Step 5. Landing Page (index.php)

```html
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Landing Page</title>
    <link rel="stylesheet" href="css/styles.css" />
  </head>
  <body>
    <h1>Welcome to our site</h1>
    <a href="signup.php">Sign Up</a>
    <a href="login.php">Login</a>
  </body>
</html>
```

---

## Step 6. Sign Up Page (signup.php)

```php
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
```

---

## Step 7. Login Page (login.php)

```php
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
```

---

## Step 8. Welcome Page (welcome.php)

```php
<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h1>Welcome, <?php echo $user['first_name'] . ' ' . $user['last_name']; ?>!</h1>
    <p>Your email: <?php echo $user['email']; ?></p>
    <a href="logout.php">Logout</a>
</body>
</html>
```

---

## Step 9. Admin Page (admin.php)

```php
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
        <?php foreach ($users as $user): ?>
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
```

---

## Step 10. Logout Page (logout.php)

```php
<?php
session_start();
session_destroy();
header('Location: login.php');
exit();
?>
```
