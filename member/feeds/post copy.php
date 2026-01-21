<?php
header('Content-Type: application/json');

// 1. Database Connection & Function Include
include __DIR__."/../../config/function.php"; 
$main = new Main($conn); 

// Authentication Check
if(!$main->isLoggedin()){
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

$current_user_id = $main->userData()['data']['id'] ?? null;
if(empty($current_user_id)){
    echo json_encode(['status' => 'error', 'message' => 'User session not found']);
    exit; 
}

// 2. Capture JSON Webhook Data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Check if action exists
$action = $data['action'] ?? '';

switch ($action) {
    case 'create-feeds':
        $content = $data['content'] ?? '';
        $media_items = $data['media'] ?? [];
        $saved_media_paths = [];

        // Processing Multiple Media Items
        if (!empty($media_items)) {
            $upload_dir = '../../uploads/feeds/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            foreach ($media_items as $item) {
                $file_data = $item['file'];
                
                // Regex to identify image type and extract base64
                if (preg_match('/^data:image\/(\w+);base64,/', $file_data, $type)) {
                    $extension = strtolower($type[1]); 
                    $file_content = base64_decode(substr($file_data, strpos($file_data, ',') + 1));

                    if ($file_content) {
                        $file_name = 'feed_' . bin2hex(random_bytes(8)) . '.' . $extension;
                        $target_path = $upload_dir . $file_name;

                        if (file_put_contents($target_path, $file_content)) {
                            $saved_media_paths[] = [
                                'type' => 'image',
                                'path' =>  $file_name,
                                'uploaded_at' => date('Y-m-d H:i:s')
                            ];
                        }
                    }
                }
            }
        }

        // Insert using Prepared Statements
        $media_json = json_encode($saved_media_paths);
        $type = 'post';
        $status = 'active';

        $stmt = $conn->prepare("INSERT INTO feeds (user_id, content, media, type, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $current_user_id, $content, $media_json, $type, $status);

        if ($stmt->execute()) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Post created successfully',
                'feed_id' => $conn->insert_id
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $stmt->error]);
        }
        $stmt->close();
        break;

    case 'delete-feed':
        // You can easily add more cases like this
        $feed_id = $data['feed_id'] ?? 0;
        // ... logic to delete feed ...
        break;

        // Inside post.php switch($action)
case 'like':
    $fid = (int)$data['feed_id'];
    $check = $conn->query("SELECT id FROM likes WHERE feed_id=$fid AND user_id=$current_user_id");
    if($check->num_rows > 0) {
        $conn->query("DELETE FROM likes WHERE feed_id=$fid AND user_id=$current_user_id");
        $type = "unliked";
    } else {
        $conn->query("INSERT INTO likes (feed_id, user_id) VALUES ($fid, $current_user_id)");
        $type = "liked";
    }
    $count = $conn->query("SELECT COUNT(*) as total FROM likes WHERE feed_id=$fid")->fetch_assoc()['total'];
    echo json_encode(['status'=>'success', 'type'=>$type, 'count'=>$count]);
    break;

case 'share':
    $fid = (int)$data['feed_id'];
    $conn->query("UPDATE feeds SET shares = shares + 1 WHERE id = $fid");
    echo json_encode(['status'=>'success']);
    break;



    case 'load-comments':
        $fid = (int)$data['feed_id'];
        // Call the recursive function from the Main class
        $html = $main->getComments($fid, 0); 
        echo json_encode(['status' => 'success', 'html' => $html]);
        break;

    case 'add-comment':
        $fid = (int)$data['feed_id'];
        $pid = (int)($data['parent_id'] ?? 0);
        $comment = htmlspecialchars($data['comment']);
        
        $stmt = $conn->prepare("INSERT INTO comments (feed_id, user_id, parent_id, comment) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $fid, $current_user_id, $pid, $comment);
        
        if($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        }
        break;

    case 'like-comment':
        $cid = (int)$data['comment_id'];
        // Toggle logic for comment likes
        $check = $conn->query("SELECT id FROM comment_likes WHERE comment_id=$cid AND user_id=$current_user_id");
        if($check->num_rows > 0) {
            $conn->query("DELETE FROM comment_likes WHERE comment_id=$cid AND user_id=$current_user_id");
            $type = "unliked";
        } else {
            $conn->query("INSERT INTO comment_likes (comment_id, user_id) VALUES ($cid, $current_user_id)");
            $type = "liked";
        }
        $count = $conn->query("SELECT COUNT(*) as total FROM comment_likes WHERE comment_id=$cid")->fetch_assoc()['total'];
        echo json_encode(['status' => 'success', 'type' => $type, 'count' => $count]);
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid or missing action']);
        break;
}

$conn->close();
?>