<?php
ini_set('display_errors', 0);                
error_reporting(E_ERROR | E_PARSE);          

require __DIR__ . '/../../../vendor/autoload.php';

// BASE_URL koristimo u doc_setup.php (@OA\Server)
if ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == '127.0.0.1') {
    // XAMPP lokalno – prilagođeno za tvoj projekat:
    // http://localhost/WEB-FULLSTACK-Fising.Store/BACKEND
    define('BASE_URL', 'http://localhost/WEB-FULLSTACK-Fising.Store/BACKEND');
} else {
    // Ako kasnije deployaš na hosting, ovdje stavi pravi URL backend-a
    define('BASE_URL', 'https://your-production-domain.com/backend');
}

// Generišemo OpenAPI specifikaciju – skeniramo doc_setup + sve ROUTES fajlove
$openapi = \OpenApi\Generator::scan([
    __DIR__ . '/doc_setup.php',
    __DIR__ . '/../../../rest/ROUTES',
]);

header('Content-Type: application/json; charset=utf-8');
echo $openapi->toJson();
