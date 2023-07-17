<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: index.php");
  exit();
}

// Include config file
require_once 'includes/config.php';

// Validate task ID
if (empty($_POST['task_id'])) {
  $response = array('success' => false, 'error' => 'Invalid request.');
} else {
  // Delete task from the database
  $sql = 'DELETE FROM tasks WHERE id = ? AND user_id = ?';
  if ($stmt = $pdo->prepare($sql)) {
    $stmt->bindParam(1, $_POST['task_id'], PDO::PARAM_INT);
    $stmt->bindParam(2, $_SESSION['user_id'], PDO::PARAM_INT);
    if ($stmt->execute()) {
      $response = array('success' => true);
    } else {
      $response = array('success' => false, 'error' => 'Something went wrong. Please try again later.');
    }
  } else {
    $response = array('success' => false, 'error' => 'Something went wrong. Please try again later.');
  }
}

// Close connection
unset($pdo);

// Return JSON response
echo json_encode($response);
