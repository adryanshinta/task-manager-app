<?php
$host = 'localhost';
$dbname = 'taskmanagerapp';
$username = 'root';
$password = '';

try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  exit('Failed to connect to database: ' . $e->getMessage());
}
