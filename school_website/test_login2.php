<?php
require 'C:\xampp\htdocs\school_website\config\db.php';
$username = 'admin';
$password = 'admin123';
$role = 'admin';

$stmt = $pdo->prepare("SELECT id, password, role FROM users WHERE username = ? AND role = ?");
$stmt->execute([$username, $role]);
$user = $stmt->fetch();
var_dump($user);
var_dump(password_verify($password, $user['password']));
?>
