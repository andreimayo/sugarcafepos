<?php
require_once __DIR__ . '/sugarcafe-api/config/config.php';
require_once __DIR__ . '/sugarcafe-api/routes/api.php';

// Set headers for CORS and JSON response
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}

// The router is already instantiated and routes are defined in api.php
// So we don't need to do anything else here, the router will handle the request

