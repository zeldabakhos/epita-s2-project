<?php
require 'db.php';  
header('Content-Type: application/json');

try {
    $sql = "SELECT 
        students.student_epita_email,
        students.student_enrollment_status,
        students.student_population_period_ref,
        students.student_population_year_ref,
        students.student_population_code_ref,
        contacts.contact_first_name,
        contacts.contact_last_name,
        contacts.contact_address,
        contacts.contact_city,
        contacts.contact_country,
        contacts.contact_birthdate
    FROM 
        students
    JOIN 
        contacts ON students.student_contact_ref = contacts.contact_email
    ;";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['students' => $students]);
    
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>