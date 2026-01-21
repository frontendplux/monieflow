<?php
include __DIR__.'/function.php';
header('Content-Type: application/json');

$endpoint = $_GET['page'] ?? '';
$data = json_decode(file_get_contents("php://input"), true);

$main = new Main($conn);

switch ($endpoint) {

    case 'login':
        echo json_encode($main->login($data));
        break;

    case 'register': 
        echo json_encode($main->register($data)); 
        break;

    case 'forgot-password': 
        echo json_encode($main->forgetPassword($data)); 
        break;

    case 'verify-code': 
        echo json_encode($main->verifyCode($data)); 
        break;

    case 'createfeeds': 
        echo json_encode($main->createFeed($_POST)); 
        break;

    /* ============================
       ✅ ADD PRODUCT CREATION HERE
       ============================ */
    case 'createproduct':
        echo json_encode($main->createProduct($_POST));
        break;

    case 'feeds': 
        echo json_encode($main->feeds()); 
        break;

    case 'fetchProducts':
        echo json_encode($main->fetchProducts());
        break;

    default:
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Endpoint not found'
        ]);
        break;
}
