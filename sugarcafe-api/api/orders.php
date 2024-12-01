<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../config/Database.php';
require_once '../config/ApiResponse.php';
require_once '../utils/Logger.php';
require_once '../utils/InputValidator.php';
require_once '../utils/Auth.php';

$database = new Database();
$db = $database->getConnection();
$method = $_SERVER['REQUEST_METHOD'];

$logger = new Logger($db);
$auth = new Auth($db);

if (!$auth->isAuthenticated()) {
    ApiResponse::send(401, "Unauthorized");
}

switch($method) {
    case 'GET':
        try {
            if(isset($_GET['id'])) {
                $stmt = $db->prepare("
                    SELECT o.*, oi.product_id, oi.quantity, oi.unit_price, oi.subtotal, p.name as product_name
                    FROM orders o
                    LEFT JOIN order_items oi ON o.id = oi.order_id
                    LEFT JOIN products p ON oi.product_id = p.id
                    WHERE o.id = ?
                ");
                $stmt->execute([$_GET['id']]);
                $order = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if($order) {
                    ApiResponse::send(200, "Order found", $order);
                } else {
                    ApiResponse::send(404, "Order not found");
                }
            } else {
                $stmt = $db->query("SELECT * FROM orders ORDER BY created_at DESC");
                $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
                ApiResponse::send(200, "Orders retrieved", $orders);
            }
        } catch(PDOException $e) {
            ApiResponse::send(500, "Error: " . $e->getMessage());
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
    
        if (!InputValidator::validate($data, ['items' => 'required', 'total_amount' => 'required'])) {
            ApiResponse::send(400, "Invalid input");
        }

        try {
            $db->beginTransaction();

            // Create order
            $stmt = $db->prepare("INSERT INTO orders (total_amount, payment_method, status) VALUES (?, ?, ?)");
            $stmt->execute([
                $data->total_amount,
                $data->payment_method ?? 'cash',
                'pending'
            ]);
            $orderId = $db->lastInsertId();

            // Create order items
            $stmt = $db->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price, subtotal) VALUES (?, ?, ?, ?, ?)");
            
            foreach($data->items as $item) {
                $stmt->execute([
                    $orderId,
                    $item->product_id,
                    $item->quantity,
                    $item->unit_price,
                    $item->quantity * $item->unit_price
                ]);

                // Update product stock
                $updateStock = $db->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
                $updateStock->execute([$item->quantity, $item->product_id]);
            }

            $db->commit();
            $logger->log('info', 'Order created', ['order_id' => $orderId]);
            ApiResponse::send(201, "Order created successfully", ['order_id' => $orderId]);
        } catch(PDOException $e) {
            $db->rollBack();
            $logger->log('error', 'Error creating order', ['error' => $e->getMessage()]);
            ApiResponse::send(500, "Error: " . $e->getMessage());
        }
        break;

    default:
        ApiResponse::send(405, "Method not allowed");
        break;
}
?>

