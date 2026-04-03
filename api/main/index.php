<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
  $data=json_decode(file_get_contents("php://input"), true) ?? $_POST ?? [];
  $action = $data['action'] ?? '';
   include __DIR__.'/../config/conn.php';
  include __dir__.'/class.php';
  $feeds = new feeds($conn);
  $auth= new auth($conn);
  $friends= new Friends($conn);

    switch ($action) {
        case 'isloggedin':
           echo json_encode($auth->userAuth($data));
          break;
        case 'login':
            echo json_encode($auth->login($data));
          break;

        case 'register':
            echo json_encode($auth->register($data));
          break;

        // ========feeds========
        case 'createFeeds':
            echo json_encode($feeds->createFeeds($data));
          break;
        
        case 'fetchFeeds':
            echo json_encode($feeds->fetchFeeds($data));
          break;
        
        case 'deleteFeeds':
            echo json_encode($feeds->deleteFeeds($data));
          break;

        case 'likeFeeds':
          //* post, user 
          echo json_encode($feeds->likeFeeds($data));
          break;

        case 'getuser':
            echo $data['type'] == 'me' ?   json_encode($auth->myProfile($data['user'])) : json_encode($auth->userProfile($data['user']));
          break;

        case 'getFriends':
            echo json_encode($friends->getFriends($data));
          break;

        default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }