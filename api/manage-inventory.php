<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");

include_once '../config/Database.php';

class Inventory {
    private $conn;
    private $table_name = "products";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all products
    public function getProducts() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Add new product
    public function addProduct($name, $description, $price, $stock_quantity, $category) {
        $query = "INSERT INTO " . $this->table_name . 
                " (name, description, price, stock_quantity, category) VALUES (?,?,?,?,?)";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$name, $description, $price, $stock_quantity, $category]);
    }

    // Update product
    public function updateProduct($id, $data) {
        $fields = [];
        $values = [];
        
        foreach($data as $key => $value) {
            if($key != 'id') {
                $fields[] = "$key = ?";
                $values[] = $value;
            }
        }
        $values[] = $id;
        
        $query = "UPDATE " . $this->table_name . " SET " . 
                implode(", ", $fields) . " WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($values);
    }

    // Delete product
    public function deleteProduct($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }
}