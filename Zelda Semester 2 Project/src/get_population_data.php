<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $query1 = "
            SELECT 
                student_population_code_ref, 
                student_population_period_ref, 
                student_population_year_ref, 
                COUNT(*) AS student_count
            FROM students
            WHERE student_enrollment_status = 'completed'
            GROUP BY student_population_code_ref, 
                     student_population_period_ref, 
                     student_population_year_ref
            ORDER BY student_population_year_ref ASC, 
                     student_population_period_ref ASC;
        ";

        $stmt1 = $pdo->query($query1);
        $results1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);

        $query2 = "
            SELECT DISTINCT program_assignment,
                (COUNT(CASE WHEN attendance_presence = '1' THEN 1 END) * 100.0 / COUNT(*)) AS PresencePercentage 
            FROM students s 
            JOIN attendance a ON s.student_epita_email = a.attendance_student_ref
            JOIN programs p ON a.attendance_course_ref = p.program_course_code_ref
            GROUP BY program_assignment;
        ";

        $stmt2 = $pdo->query($query2);
        $results2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(["success" => true, "students" => $results1, "attendance" => $results2]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    }
}
?>
