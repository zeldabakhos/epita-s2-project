<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'projectdb2');

try {
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->prepare("SELECT 
    student_population_code_ref, 
    student_population_period_ref, 
    student_population_year_ref, 
    COUNT(*) AS student_count
FROM 
    students 
GROUP BY 
    student_population_code_ref, 
    student_population_period_ref, 
    student_population_year_ref
ORDER BY 
    student_population_year_ref ASC, 
    student_population_period_ref ASC");
    $stmt->execute();
    
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if($data) {
        echo json_encode(array("success" => true, "data" => $data));
    } else {
        echo json_encode(array("success" => false, "message" => "No data found."));
    }
    
} catch (PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}
?>
