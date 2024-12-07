<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'sugarcafe_pos');
define('DB_USER', 'root');
define('DB_PASS', 'sugarcafe');

// API configuration
define('API_BASE_PATH', '/sugarcafe-api');

// Logging configuration
define('LOG_FILE', __DIR__ . '/../logs/app.log');
define('LOG_LEVEL', 'debug'); // Options: debug, info, warning, error

// JWT configuration for authentication
define('JWT_SECRET', 'your_jwt_secret_key_here');
define('JWT_EXPIRATION', 3600); // 1 hour

// CORS configuration
define('ALLOWED_ORIGINS', ['http://localhost:3000', 'https://yourdomain.com']);

// Error reporting
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// Timezone
date_default_timezone_set('UTC');

