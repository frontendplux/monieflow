<?php
session_start();
include __DIR__."/config/function.php"; // this should include your DB connection and Main class
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $action = $_POST['action'] ?? '';
    $main = new Main($conn);
    switch ($action) {
        case 'login':
            // Collect username and password from POST
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $result = $main->login([$username, $password]);
            echo json_encode($result);
            break;

        case 'signup': 
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? ''; 
            $password = $_POST['password'] ?? ''; 
            $result = $main->signup([$username, $email, $password]); 
            echo json_encode(["success"=>$result[0], "message"=>$result[1]]); 
            break;
        
        case 'confirm': 
            $result = $main->confirmCode([$_POST['code'] ?? '']); 
            echo json_encode(["success"=>$result[0], "message"=>$result[1]]); 
            break;

        case 'transfer': 
            $recipientId = $_POST['recipient_id'] ?? null;
                $walletId = $_POST['wallet_id'] ?? null;
                $amount = $_POST['amount'] ?? null;
                // validate inputs
                if (!$recipientId || !$walletId || !$amount) { 
                    echo json_encode(["success" => false, "message" => "Missing required fields"]);
                        exit; 
                    } 
                // call your transfer function 
                $result = $main->transferMoney([$recipientId, $walletId, $amount]); 
                echo json_encode($result); 
                break;
        
        case 'redeem':
            echo $code = $_POST['card_code'] ?? '';
            $result = $main->redeemCard($code);
            echo json_encode($result);
            break;

            
        default:
            echo json_encode([
                "success" => false,
                "message" => "Invalid action"
            ]);
            break;
    }
}
