<?php
class Logger {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function log($level, $message, $context = []) {
        $stmt = $this->db->prepare("INSERT INTO logs (level, message, context) VALUES (?, ?, ?)");
        $stmt->execute([$level, $message, json_encode($context)]);
    }
}
?>

