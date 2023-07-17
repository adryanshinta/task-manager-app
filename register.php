<?php
session_start();
if (isset($_SESSION['user_id'])) {
  header("Location: dashboard.php");
  exit();
}

// Include config file
require_once 'includes/config.php';

// Define variables and initialize with empty values
$username = $password = $confirm_password = '';
$username_err = $password_err = $confirm_password_err = '';

// Processing form data when form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Validate username
  if (empty(trim($_POST['username']))) {
    $username_err = 'Please enter a username.';
  } else {
    // Prepare a select statement
    $sql = 'SELECT id FROM users WHERE username = ?';

    if ($stmt = $pdo->prepare($sql)) {
      $stmt->bindParam(1, $param_username, PDO::PARAM_STR);

      // Set parameters
      $param_username = trim($_POST['username']);

      // Attempt to execute the prepared statement
      if ($stmt->execute()) {
        if ($stmt->rowCount() == 1) {
          $username_err = 'This username is already taken.';
        } else {
          $username = trim($_POST['username']);
        }
      } else {
        echo 'Oops! Something went wrong. Please try again later.';
      }
    }

    // Close statement
    unset($stmt);
  }

  // Validate password
  if (empty(trim($_POST['password']))) {
    $password_err = 'Please enter a password.';
  } elseif (strlen(trim($_POST['password'])) < 6) {
    $password_err = 'Password must have at least 6 characters.';
  } else {
    $password = trim($_POST['password']);
  }

  // Validate confirm password
  if (empty(trim($_POST['confirm_password']))) {
    $confirm_password_err = 'Please confirm password.';
  } else {
    $confirm_password = trim($_POST['confirm_password']);
    if (empty($password_err) && ($password != $confirm_password)) {
      $confirm_password_err = 'Password did not match.';
    }
  }

  // Check input errors before inserting in database
  if (empty($username_err) && empty($password_err) && empty($confirm_password_err)) {
    // Prepare an insert statement
    $sql = 'INSERT INTO users (username, password) VALUES (?, ?)';

    if ($stmt = $pdo->prepare($sql)) {
      $stmt->bindParam(1, $param_username, PDO::PARAM_STR);
      $stmt->bindParam(2, $param_password, PDO::PARAM_STR);

      // Set parameters
      $param_username = $username;
      $param_password = md5($password); // Apply MD5 hashing to password

      // Attempt to execute the prepared statement
      if ($stmt->execute()) {
        // Redirect to login page
        header('Location: login.php');
        exit();
      } else {
        echo 'Oops! Something went wrong. Please try again later.';
      }
    }

    // Close statement
    unset($stmt);
  }

  // Close connection
  unset($pdo);
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Task Manager App - Register</title>
  <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
  <header>
    <div class="container">
      <h1>Task Manager App</h1>
    </div>
  </header>
  <div class="container">
    <h2>Register</h2>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
      <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" value="<?php echo $username; ?>">
        <span class="help-block"><?php echo $username_err; ?></span>
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" value="<?php echo $password; ?>">
        <span class="help-block"><?php echo $password_err; ?></span>
      </div>
      <div class="form-group">
        <label>Confirm Password</label>
        <input type="password" name="confirm_password" value="<?php echo $confirm_password; ?>">
        <span class="help-block"><?php echo $confirm_password_err; ?></span>
      </div>
      <div class="form-group">
        <input type="submit" value="Register">
      </div>
      <p>Already have an account? <a href="login.php">Login</a></p>
    </form>
  </div>
</body>
</html>
