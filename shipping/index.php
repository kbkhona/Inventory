<?php
require_once './src/Auth.php';
require_once './src/ProductController.php';

// Initialize Auth for JWT decoding
Auth::init();

// Get the Authorization header (contains the JWT token)
$headers = getallheaders();
$jwt = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;

// Validate the JWT token and role at the entry point
try {
    // Check if the JWT token exists
    if (!$jwt) {
        http_response_code(401);  // Unauthorized
        echo json_encode(['status' => 'error', 'message' => 'Missing Authorization header']);
        exit;
    }

    // Validate the token and the role
    $userData = Auth::validateToken($jwt);
    $validRole = $userData['role']; // Assuming the role is included in the token
    error_log('user validated - role' . $validRole);
    // Proceed with the request if the token is valid and the role is correct
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $requestMethod = $_SERVER['REQUEST_METHOD'];

    $controller = new ProductController();

    // Routing logic based on the URI path
    switch ($uri) {
        case '/api/getShippingRate':
            if ($requestMethod === 'POST') {
                $data = json_decode(file_get_contents('php://input'), true);
                if (!isset($data['sku']) || !isset($data['destination'])) {
                    http_response_code(400);  // Bad request
                    echo json_encode(['status' => 'error', 'message' => 'Missing SKU or destination']);
                    exit;
                }
                $response = $controller->getShippingRate($data['sku'], $data['destination']);
                echo $response; // Return JSON response directly
            } else {
                http_response_code(405); // Method Not Allowed
                echo json_encode(["message" => "Method Not Allowed"]);
            }
            break;

        case '/api/submit-shipment':
            if ($requestMethod === 'POST') {
                $data = json_decode(file_get_contents('php://input'), true);
                if (!isset($data['sku']) || !isset($data['quantity'])) {
                    http_response_code(400);  // Bad request
                    echo json_encode(['status' => 'error', 'message' => 'Missing SKU or quantity']);
                    exit;
                }
                $response = $controller->submitShipmentOrder($data['sku'], $data['quantity']);
                echo $response; // Return JSON response directly
            } else {
                http_response_code(405); // Method Not Allowed
                echo json_encode(["message" => "Method Not Allowed"]);
            }
            break;

        default:
            http_response_code(404);  // Not found
            echo json_encode(['status' => 'error', 'message' => 'Endpoint not found']);
    }

} catch (UnauthorizedException $e) {
    http_response_code(401);  // Unauthorized response
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
} catch (InvalidTokenException $e) {
    http_response_code(401);  // Unauthorized response for invalid token
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(400);  // Bad request for other errors
    echo json_encode(['status' => 'error', 'message' => 'Bad request: ' . $e->getMessage()]);
}
