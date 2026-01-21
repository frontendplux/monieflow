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
$action = $data['action'] ?? $_GET['action'];

switch ($action) {
    case 'get-count':
        $id = (int)($_GET['id'] ?? 0);
        $type = $_GET['type'] ?? 'feed';
        
        if ($type === 'feed') {
            $stmt = $conn->prepare("SELECT COUNT(*) as total FROM likes WHERE feed_id = ?");
        } else {
            $stmt = $conn->prepare("SELECT COUNT(*) as total FROM likes WHERE comment_id = ?");
        }
        
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $total = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
        
        echo json_encode(['count' => (int)$total]);
        break;
    
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



    case 'add-comment':
        $fid = (int)$data['feed_id'];
        $rid = !empty($data['reply_id']) ? (int)$data['reply_id'] : null;
        $content = htmlspecialchars($data['content']);

        $stmt = $conn->prepare("INSERT INTO comments (feed_id, user_id, reply_id, content) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $fid, $current_user_id, $rid, $content);
        if($stmt->execute()) echo json_encode(['status' => 'success']);
        break;



    case 'load-comments':
        $fid = (int)$data['feed_id'];
        $html = $main->getComments($fid); // Your recursive function
        
        // Get total count including all nested replies
        $res = $conn->query("SELECT COUNT(*) as total FROM comments WHERE feed_id = $fid AND status = 'active'");
        $total = $res->fetch_assoc()['total'];
        
        echo json_encode([
            'status' => 'success', 
            'html' => $html, 
            'total' => $total
        ]);
    break;

    case 'like-comment':
        $cid = (int)($data['comment_id'] ?? 0);
        
        // FIXED: Using $current_user_id instead of undefined $viewer_id
        $stmt = $conn->prepare("SELECT id FROM likes WHERE comment_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $cid, $current_user_id);
        $stmt->execute();
        $check = $stmt->get_result();

        if ($check->num_rows > 0) {
            $act = $conn->prepare("DELETE FROM likes WHERE comment_id = ? AND user_id = ?");
            $status = "unliked";
        } else {
            $act = $conn->prepare("INSERT INTO likes (comment_id, user_id) VALUES (?, ?)");
            $status = "liked";
        }
        $act->bind_param("ii", $cid, $current_user_id);
        $act->execute();

        $count = $conn->query("SELECT COUNT(*) as total FROM likes WHERE comment_id = $cid")->fetch_assoc()['total'];
        echo json_encode(['status' => 'success', 'type' => $status, 'count' => (int)$count]);
        break;

        case 'delete-comment':
            $cid = (int)($data['comment_id'] ?? 0);

            // Security: Check if comment exists AND belongs to the current user
            $stmt = $conn->prepare("SELECT id FROM comments WHERE id = ? AND user_id = ?");
            $stmt->bind_param("ii", $cid, $current_user_id);
            $stmt->execute();
            $res = $stmt->get_result();

            if ($res->num_rows > 0) {
                // Option A: Hard Delete (Removes completely)
                $del = $conn->prepare("DELETE FROM comments WHERE id = ? OR reply_id = ?");
                $del->bind_param("ii", $cid, $cid); // Deletes the comment and its direct replies
                
                if ($del->execute()) {
                    echo json_encode(['status' => 'success']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Database error']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Permission denied or comment not found']);
            }
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid or missing action']);
        break;
}

$conn->close();
?>