<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->items) && !empty($data->total_amount)) {
    try {
        $db->beginTransaction();
        
        // Create order
        $order_query = "INSERT INTO orders SET 
                       order_number=:order_number, 
                       total_amount=:total_amount";
        
        $stmt = $db->prepare($order_query);
        
        $order_number = 'ORD-' . time();
        $total_amount = htmlspecialchars(strip_tags($data->total_amount));
        
        $stmt->bindParam(":order_number", $order_number);
        $stmt->bindParam(":total_amount", $total_amount);
        
        $stmt->execute();
        $order_id = $db->lastInsertId();
        
        // Create order items
        foreach($data->items as $item) {
            $item_query = "INSERT INTO order_items SET 
                          order_id=:order_id, 
                          product_id=:product_id, 
                          quantity=:quantity, 
                          unit_price=:unit_price, 
                          subtotal=:subtotal";
            
            $stmt = $db->prepare($item_query);
            
            $stmt->bindParam(":order_id", $order_id);
            $stmt->bindParam(":product_id", $item->product_id);
            $stmt->bindParam(":quantity", $item->quantity);
            $stmt->bindParam(":unit_price", $item->unit_price);
            $stmt->bindParam(":subtotal", $item->subtotal);
            
            $stmt->execute();
        }
        
        $db->commit();
        
        http_response_code(201);
        echo json_encode(array(
            "message" => "Order was created successfully.",
            "order_number" => $order_number
        ));
        
    } catch(PDOException $e) {
        $db->rollBack();
        http_response_code(503);
        echo json_encode(array("message" => "Unable to create order: " . $e->getMessage()));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Unable to create order. Data is incomplete."));
}
?>