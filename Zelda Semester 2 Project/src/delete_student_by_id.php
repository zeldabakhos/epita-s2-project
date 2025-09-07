<?php
require 'db.php';

function delete_student_by_id($student_id) {
    global $pdo;

    try {
        $sql = "SELECT student_contact_ref FROM students WHERE student_epita_email = :student_id";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':student_id', $student_id);

        $stmt->execute();

        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($student) {
            $student_contact_ref = $student['student_contact_ref'];

            $delete_grades_sql = "DELETE FROM grades WHERE grade_student_epita_email_ref = :student_id";
            
            $delete_grades_stmt = $pdo->prepare($delete_grades_sql);
            
            $delete_grades_stmt->bindParam(':student_id', $student_id);
            
            $delete_grades_stmt->execute();
            
            $delete_student_sql = "DELETE FROM students WHERE student_epita_email = :student_id";

            $delete_student_stmt = $pdo->prepare($delete_student_sql);

            $delete_student_stmt->bindParam(':student_id', $student_id);

            $delete_student_stmt->execute();

            if ($delete_student_stmt->rowCount() > 0) {
                $delete_contact_sql = "DELETE FROM contacts WHERE contact_email = :contact_email";

                $delete_contact_stmt = $pdo->prepare($delete_contact_sql);

                $delete_contact_stmt->bindParam(':contact_email', $student_contact_ref);

                $delete_contact_stmt->execute();

                if ($delete_contact_stmt->rowCount() > 0) {
                    echo json_encode(['success' => true, 'message' => 'Student, grades, and associated contact deleted successfully']);
                } else {
                    echo json_encode(['success' => true, 'message' => 'Student and grades deleted but no associated contact found']);
                }

            } else {
                echo json_encode(['success' => false, 'message' => 'No student found with the given email']);
            }

        } else {
            echo json_encode(['success' => false, 'message' => 'No student found with the given email']);
        }

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
}


if (isset($_POST['student_epita_email'])) {
    delete_student_by_id($_POST['student_epita_email']);
} else {
    echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
}
?>
