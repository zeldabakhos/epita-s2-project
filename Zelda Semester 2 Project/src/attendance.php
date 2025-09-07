<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $query = "
        SELECT DISTINCT program_assignment,
        (COUNT(CASE WHEN attendance_presence = '1' THEN 1 END) * 100.0 / COUNT(*)) AS PresencePercentage 
        FROM students s 
        JOIN attendance a ON attendance_student_ref = student_epita_email
        JOIN programs p ON attendance_course_ref = program_course_code_ref
        GROUP BY program_assignment;
        ";

        $stmt = $pdo->query($query);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["success" => true, "attendance" => $rows]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    }
}
?>
