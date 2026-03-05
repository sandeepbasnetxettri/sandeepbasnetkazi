<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_name = $_POST['student_name'] ?? '';
    $parent_name = $_POST['parent_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $class_applied = $_POST['class_applied'] ?? '';

    if (!empty($student_name) && !empty($parent_name) && !empty($email)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO inquiries (student_name, parent_name, email, phone, class_applied) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$student_name, $parent_name, $email, $phone, $class_applied]);
            
            // Redirect back with success message
            header("Location: ../admission.php?status=success#apply");
            exit;
        } catch (PDOException $e) {
            header("Location: ../admission.php?status=error#apply");
            exit;
        }
    } else {
        header("Location: ../admission.php?status=missing#apply");
        exit;
    }
} else {
    header("Location: ../admission.php");
    exit;
}
?>
