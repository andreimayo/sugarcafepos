<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

$query = "SELECT s.sale_date, SUM(s.total_amount) as daily_total, 
          COUNT(s.id) as num_transactions 
          FROM sales s 
          WHERE s.sale_date BETWEEN :start_date AND :end_date 
          GROUP BY s.sale_date 
          ORDER BY s.sale_date";

try {
    $stmt = $db->prepare($query);
    $stmt->bindParam(":start_date", $start_date);
    $stmt->bindParam(":end_date", $end_date);
    $stmt->execute();
    
    $sales_report = array();
    $total_sales = 0;
    $total_transactions = 0;
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $sales_report[] = $row;
        $total_sales += $row['daily_total'];
        $total_transactions += $row['num_transactions'];
    }
    
    $response = array(
        "start_date" => $start_date,
        "end_date" => $end_date,
        "total_sales" => $total_sales,
        "total_transactions" => $total_transactions,
        "daily_report" => $sales_report
    );
    
    http_response_code(200);
    echo json_encode($response);
} catch(PDOException $e) {
    http_response_code(503);
    echo json_encode(array("message" => "Unable to retrieve sales report: " . $e->getMessage()));
}
?>