<?php
require 'db.php';

function fetchStudentsData($discipline, $year, $period) {
    global $pdo;

    try {
        $sql = "SELECT s.student_epita_email, 
        s.student_enrollment_status, 
        s.student_population_period_ref, 
        s.student_population_year_ref, 
        c.contact_first_name, 
        c.contact_last_name, 
        c.contact_email, 
        c.contact_address, 
        c.contact_city, 
        c.contact_country, 
        c.contact_birthdate,
        COUNT(DISTINCT CASE WHEN course_averages.avg_grade > 10 THEN course_averages.grade_course_code_ref ELSE NULL END) AS passed
 FROM students s
 INNER JOIN contacts c ON s.student_contact_ref = c.contact_email
 LEFT JOIN (
     SELECT
         g.grade_student_epita_email_ref,
         g.grade_course_code_ref,
         AVG(g.grade_score) AS avg_grade
     FROM grades g
     GROUP BY g.grade_student_epita_email_ref, g.grade_course_code_ref
 ) AS course_averages ON s.student_epita_email = course_averages.grade_student_epita_email_ref
 WHERE s.student_population_code_ref = :discipline
   AND s.student_population_year_ref = :year
   AND s.student_population_period_ref = :period
 GROUP BY s.student_epita_email, 
          s.student_enrollment_status, 
          s.student_population_period_ref, 
          s.student_population_year_ref, 
          c.contact_first_name, 
          c.contact_last_name, 
          c.contact_email, 
          c.contact_address, 
          c.contact_city, 
          c.contact_country, 
          c.contact_birthdate;
 ";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':discipline', $discipline);
        $stmt->bindParam(':year', $year);
        $stmt->bindParam(':period', $period);

        $stmt->execute();

        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'students' => $students]);

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
}

if (isset($_POST['discipline']) && isset($_POST['year']) && isset($_POST['period'])) {
    fetchStudentsData($_POST['discipline'], $_POST['year'], $_POST['period']);
} else {
    echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
}
?>
