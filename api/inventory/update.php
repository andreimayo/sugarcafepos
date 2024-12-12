<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->product_id) && isset($data->quantity)) {
    $query = "INSERT INTO inventory (product_id, quantity) 
              VALUES (:product_id, :quantity) 
              ON DUPLICATE KEY UPDATE quantity = :quantity";
    
    try {
        $stmt = $db->prepare($query);
        
        $stmt->bindParam(":product_id", $data->product_id);
        $stmt->bindParam(":quantity", $data->quantity);
        
        if ($stmt->execute()) {
            http_response_code(200);
            echo json_encode(array("message" => "Inventory updated successfully."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to update inventory."));
        }
    } catch(PDOException $e) {
        http_response_code(503);
        echo json_encode(array("message" => "Unable to update inventory: " . $e->getMessage()));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Unable to update inventory. Data is incomplete."));
}
?>