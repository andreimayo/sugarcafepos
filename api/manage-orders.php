<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");

include_once '../config/Database.php';

class Orders {
    private $conn;
    private $orders_table = "orders";
    private $order_items_table = "order_items";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create new order
    public function createOrder($user_id, $items) {
        try {
            $this->conn->beginTransaction();

            // Calculate total amount
            $total_amount = 0;
            foreach($items as $item) {
                $total_amount += $item->quantity * $item->price;
            }

            // Create order
            $query = "INSERT INTO " . $this->orders_table . 
                    " (user_id, total_amount) VALUES (?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$user_id, $total_amount]);
            $order_id = $this->conn->lastInsertId();

            // Add order items
            $query = "INSERT INTO " . $this->order_items_table . 
                    " (order_id, product_id, quantity, price_at_time) VALUES (?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            
            foreach($items as $item) {
                $stmt->execute([
                    $order_id,
                    $item->product_id,
                    $item->quantity,
                    $item->price
                ]);

                // Update inventory
                $this->updateInventory($item->product_id, $item->quantity);
            }

            $this->conn->commit();
            return [
                'success' => true,
                'order_id' => $order_id
            ];
        } catch(Exception $e) {
            $this->conn->rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    // Get order details
    public function getOrder($order_id) {
        $query = "SELECT o.*, oi.product_id, oi.quantity, oi.price_at_time, 
                p.name as product_name 
                FROM " . $this->orders_table . " o
                JOIN " . $this->order_items_table . " oi ON o.id = oi.order_id
                JOIN products p ON oi.product_id = p.id
                WHERE o.id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$order_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Edit order
    public function editOrder($order_id, $items) {
        try {
            $this->conn->beginTransaction();

            // Calculate new total amount
            $total_amount = 0;
            foreach($items as $item) {
                $total_amount += $item->quantity * $item->price;
            }

            // Update order total
            $query = "UPDATE " . $this->orders_table . " SET total_amount = ? WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$total_amount, $order_id]);

            // Delete existing order items
            $query = "DELETE FROM " . $this->order_items_table . " WHERE order_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$order_id]);

            // Add new order items
            $query = "INSERT INTO " . $this->order_items_table . 
                    " (order_id, product_id, quantity, price_at_time) VALUES (?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            
            foreach($items as $item) {
                $stmt->execute([
                    $order_id,
                    $item->product_id,
                    $item->quantity,
                    $item->price
                ]);

                // Update inventory
                $this->updateInventory($item->product_id, $item->quantity);
            }

            $this->conn->commit();
            return [
                'success' => true,
                'message' => 'Order updated successfully'
            ];
        } catch(Exception $e) {
            $this->conn->rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    // Delete order
    public function deleteOrder($order_id) {
        try {
            $this->conn->beginTransaction();

            // Delete order items
            $query = "DELETE FROM " . $this->order_items_table . " WHERE order_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$order_id]);

            // Delete order
            $query = "DELETE FROM " . $this->orders_table . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$order_id]);

            $this->conn->commit();
            return [
                'success' => true,
                'message' => 'Order deleted successfully'
            ];
        } catch(Exception $e) {
            $this->conn->rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    // Get sales data for admin dashboard
    public function getSalesData($start_date, $end_date) {
        $query = "SELECT DATE(o.created_at) as date, SUM(o.total_amount) as total_sales, 
                COUNT(DISTINCT o.id) as order_count
                FROM " . $this->orders_table . " o
                WHERE o.created_at BETWEEN ? AND ?
                GROUP BY DATE(o.created_at)
                ORDER BY DATE(o.created_at)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$start_date, $end_date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update inventory after order
    private function updateInventory($product_id, $quantity) {
        $query = "UPDATE products SET stock_quantity = stock_quantity - ? 
                WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$quantity, $product_id]);
    }
}

// Handle API requests
$database = new Database();
$db = $database->getConnection();
$orders = new Orders($db);

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        if(isset($_GET['id'])) {
            $result = $orders->getOrder($_GET['id']);
        } elseif(isset($_GET['start_date']) && isset($_GET['end_date'])) {
            $result = $orders->getSalesData($_GET['start_date'], $_GET['end_date']);
        } else {
            $result = ['error' => 'Invalid request'];
        }
        break;
    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        $result = $orders->createOrder($data->user_id, $data->items);
        break;
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        $result = $orders->editOrder($data->order_id, $data->items);
        break;
    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));
        $result = $orders->deleteOrder($data->order_id);
        break;
    default:
        $result = ['error' => 'Invalid request method'];
        break;
}

echo json_encode($result);