<?php
require 'C:\xampp\htdocs\school_website\config\db.php';
$stmt = $pdo->query("SELECT * FROM users WHERE username='admin'");
$user = $stmt->fetch();
var_dump($user);
echo "Verify result: ";
var_dump(password_verify('admin123', $user['password']));
?>
