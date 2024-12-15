<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST");

include_once '../config/Database.php';

class ViewOrders {
    private $conn;
    private $orders_table = "orders";
    private $order_items_table = "order_items";
    private $products_table = "products";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all orders
    public function getOrders($status = null, $start_date = null, $end_date = null) {
        $query = "SELECT o.id, o.user_id, o.total_amount, o.status, o.created_at,
                         u.username as cashier_name
                  FROM " . $this->orders_table . " o
                  LEFT JOIN users u ON o.user_id = u.id
                  WHERE 1=1";
        
        $params = [];

        if ($status) {
            $query .= " AND o.status = ?";
            $params[] = $status;
        }

        if ($start_date && $end_date) {
            $query .= " AND o.created_at BETWEEN ? AND ?";
            $params[] = $start_date;
            $params[] = $end_date;
        }

        $query .= " ORDER BY o.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get order details
    public function getOrderDetails($order_id) {
        $query = "SELECT oi.id, oi.product_id, p.name as product_name, oi.quantity, oi.price_at_time
                  FROM " . $this->order_items_table . " oi
                  JOIN " . $this->products_table . " p ON oi.product_id = p.id
                  WHERE oi.order_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$order_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Submit sales (change order status to completed)
    public function submitSales($order_ids) {
        try {
            $this->conn->beginTransaction();

            $query = "UPDATE " . $this->orders_table . " SET status = 'completed' WHERE id IN (" . implode(',', array_fill(0, count($order_ids), '?')) . ")";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute($order_ids);

            $this->conn->commit();
            return ['success' => true, 'message' => 'Sales submitted successfully'];
        } catch (Exception $e) {
            $this->conn->rollBack();
            return ['success' => false, 'message' => 'Error submitting sales: ' . $e->getMessage()];
        }
    }
}

// Handle API requests
$database = new Database();
$db = $database->getConnection();
$viewOrders = new ViewOrders($db);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'get_orders':
                    $status = isset($_GET['status']) ? $_GET['status'] : null;
                    $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : null;
                    $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : null;
                    $result = $viewOrders->getOrders($status, $start_date, $end_date);
                    break;
                case 'get_order_details':
                    if (isset($_GET['order_id'])) {
                        $result = $viewOrders->getOrderDetails($_GET['order_id']);
                    } else {
                        $result = ['error' => 'Order ID is required'];
                    }
                    break;
                default:
                    $result = ['error' => 'Invalid action'];
                    break;
            }
        } else {
            $result = ['error' => 'Action is required'];
        }
        break;
    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        if (isset($data->action) && $data->action === 'submit_sales') {
            if (isset($data->order_ids) && is_array($data->order_ids)) {
                $result = $viewOrders->submitSales($data->order_ids);
            } else {
                $result = ['error' => 'Order IDs are required'];
            }
        } else {
            $result = ['error' => 'Invalid action'];
        }
        break;
    default:
        $result = ['error' => 'Invalid request method'];
        break;
}

echo json_encode($result);