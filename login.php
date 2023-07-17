<?php
session_start();
if (isset($_SESSION['user_id'])) {
  header("Location: dashboard.php");
  exit();
}

// Include config file
require_once 'includes/config.php';

// Define variables and initialize with empty values
$username = $password = '';
$username_err = $password_err = '';

// Processing form data when form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Check if username is empty
  if (empty(trim($_POST['username']))) {
    $username_err = 'Please enter username.';
  } else {
    $username = trim($_POST['username']);
  }

  // Check if password is empty
  if (empty(trim($_POST['password']))) {
    $password_err = 'Please enter your password.';
  } else {
    $password = trim($_POST['password']);
  }

  // Validate credentials
  if (empty($username_err) && empty($password_err)) {
    $sql = 'SELECT id, username, password FROM users WHERE username = ?';

    if ($stmt = $pdo->prepare($sql)) {
      $stmt->bindParam(1, $username, PDO::PARAM_STR);

      if ($stmt->execute()) {
        // Check if username exists, if yes then verify password
        if ($stmt->rowCount() == 1) {
          if ($row = $stmt->fetch()) {
            $id = $row['id'];
            $username = $row['username'];
            $hashed_password = $row['password'];
            if (md5($password) == $hashed_password) { // Compare hashed password with entered password
              // Password is correct, start a new session
              session_start();

              // Store data in session variables
              $_SESSION['user_id'] = $id;
              $_SESSION['username'] = $username;

              // Redirect user to dashboard page
              header('Location: dashboard.php');
              exit();
            } else {
              // Password is not valid
              $password_err = 'Invalid password.';
            }
          }
        } else {
          // Username doesn't exist
          $username_err = 'Invalid username.';
        }
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
  <title>Task Manager App - Login</title>
  <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
  <header>
    <div class="container">
      <h1>Task Manager App</h1>
    </div>
  </header>
  <div class="container">
    <h2>Login</h2>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
      <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" value="<?php echo $username; ?>">
        <span class="help-block"><?php echo $username_err; ?></span>
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password">
        <span class="help-block"><?php echo $password_err; ?></span>
      </div>
      <div class="form-group">
        <input type="submit" value="Login">
      </div>
      <p>Don't have an account? <a href="register.php">Register</a></p>
    </form>
  </div>
</body>
</html>
