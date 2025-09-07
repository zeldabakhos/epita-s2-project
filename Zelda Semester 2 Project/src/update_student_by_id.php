<?php
require 'db.php';

function udpate_student_by_id($student_epita_email, $first_name, $last_name) {
    global $pdo;

    try {
        $stmt = $pdo->prepare("SELECT student_contact_ref FROM students WHERE student_epita_email = :email");
        $stmt->execute(['email' => $student_epita_email]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($student) {
            $student_contact_ref = $student['student_contact_ref'];

            $updateStmt = $pdo->prepare("
                UPDATE contacts 
                SET contact_first_name = :first_name, contact_last_name = :last_name 
                WHERE contact_email = :contact_email
            ");
            $updateStmt->execute([
                'first_name' => $first_name,
                'last_name' => $last_name,
                'contact_email' => $student_contact_ref
            ]);

            echo json_encode(['success' => true, 'message' => 'Contact updated successfully' , 'contact_email' => $student_contact_ref]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Student not found','contact_email' => $student]);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

if (isset($_POST['student_epita_email']) && isset($_POST['first_name']) && isset($_POST['last_name'])){
    udpate_student_by_id($_POST['student_epita_email'], $_POST['first_name'], $_POST['last_name']);
} else {
    echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
}
?>
