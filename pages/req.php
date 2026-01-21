<?php
header('Content-Type: application/json');

require __DIR__ . '/../config/function.php';
$main = new Main($conn);

$path = json_decode(file_get_contents('php://input'), true);

if (!$path || !isset($path['action'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

switch ($path['action']) {

    case 'login':
        echo json_encode($main->login([$path['emailOrPhone'],$path['password']]));
        break;

    case 'signup':
        echo json_encode($main->register($path));
        break;

    case 'findAccount':
        echo json_encode($main->forgetPassword($path['email'] ?? ''));
        break;
    
    case 'cp':
        echo json_encode($main->verifyCode($path['code'] ?? ''));
        break;

    case 'status':
        echo json_encode($main->isLoggedin());
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action']);
        break;
}
