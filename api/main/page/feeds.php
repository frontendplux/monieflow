<?php
  class feeds{
    
      private $conn;
      public function __construct($conn) {
         $this->conn = $conn;
      }

public function createFeeds($data) {
    $user_id = explode('-', $data['user'])[1] ?? 0;
    $text = $data['text'] ?? '';
    $auth = new auth($this->conn);
    $edit = $data['edit'] ?? false;
    $post_id = $data['post'] ?? 0; 
    $islogin = $auth->myProfile($data['user'])['success'] ?? false;

    if (!$islogin) {
        return ['success' => false, 'message' => 'Unauthorized'];
    }

    // ✅ Validate: must have text or at least one file
    if (empty($text) 
        && empty($_FILES['images']['name'][0]) 
        && empty($_FILES['video']['name']) 
        && empty($_FILES['audio']['name'])) {
        return ['success' => false, 'message' => 'Post cannot be empty'];
    }

    // Ensure uploads folder exists
    $uploadDir = __DIR__ . "/../../../uploads";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $uploadedImages = [];

    // ✅ Handle multiple images
    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
            $name = $_FILES['images']['name'][$key];
            $ext = pathinfo($name, PATHINFO_EXTENSION);

            $uniqueId = uniqid();
            $newName = $uniqueId . "_" . $user_id . "." . $ext;
            $uploadPath = $uploadDir . "/" . $newName;

            if (move_uploaded_file($tmpName, $uploadPath)) {
                $uploadedImages[] = $newName; // relative path for DB
            }
        }
    }

    $imagesJson = json_encode($uploadedImages);

    // ✅ Handle video
    $video = null;
    if (!empty($_FILES['video']['tmp_name'])) {
        $videoName = uniqid() . "_" . $user_id . "." . pathinfo($_FILES['video']['name'], PATHINFO_EXTENSION);
        $videoPath = $uploadDir . "/" . $videoName;
        if (move_uploaded_file($_FILES['video']['tmp_name'], $videoPath)) {
            $video = $videoName;
        }
    }

    // ✅ Handle audio
    $audio = null;
    if (!empty($_FILES['audio']['tmp_name'])) {
        $audioName = uniqid() . "_" . $user_id . "." . pathinfo($_FILES['audio']['name'], PATHINFO_EXTENSION);
        $audioPath = $uploadDir . "/" . $audioName;
        if (move_uploaded_file($_FILES['audio']['tmp_name'], $audioPath)) {
            $audio = $audioName;
        }
    }

    if ($edit && $post_id > 0) {
        // ✅ Editing existing feed
        $queryOld = "SELECT image FROM feeds WHERE id = ? AND user_id = ?";
        $smtOld = $this->conn->prepare($queryOld);
        $smtOld->bind_param("ii", $post_id, $user_id);
        $smtOld->execute();
        $resultOld = $smtOld->get_result();
        $oldRow = $resultOld->fetch_assoc();

        if ($oldRow && !empty($oldRow['image'])) {
            $oldImages = json_decode($oldRow['image'], true);
            if (is_array($oldImages)) {
                foreach ($oldImages as $oldImg) {
                    $oldPath = __DIR__ . "/" . $oldImg;
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
            }
        }

        $query = "UPDATE feeds 
                  SET content = ?, image = ?, video = ?, audio = ?, updated_at = NOW() 
                  WHERE id = ? AND user_id = ?";
        $smt = $this->conn->prepare($query);
        $smt->bind_param("ssssii", $text, $imagesJson, $video, $audio, $post_id, $user_id);
        $smt->execute();

        return [
            "success" => true,
            "message" => "Feed updated successfully",
            "feed_id" => $post_id,
            "images" => $uploadedImages
        ];
    } else {
        // ✅ Create new feed
        $query = "INSERT INTO feeds (user_id, content, image, video, audio, created_at) 
                  VALUES (?, ?, ?, ?, ?, NOW())";
        $smt = $this->conn->prepare($query);
        $smt->bind_param("issss", $user_id, $text, $imagesJson, $video, $audio);
        $smt->execute();

        $insertId = $this->conn->insert_id;

        return [
            "success" => true,
            "message" => "Feed created successfully",
            "feed_id" => $insertId,
            "images" => $uploadedImages
        ];
    }
}

public function fetchFeeds($data) {
    $authuser = new auth($this->conn);
    $isUser = $authuser->userProfile($data['user']);
    $user_id = explode('-', $data['user'])[1] ?? 0;
    $type = $data['type'];
    $offset = $data['offset'] ?? 0;
    $limit = $data['limit'] ?? 10;

    if ($type == "all") {
        $query = "SELECT 
                  F.*,
                  IF(F.user_id = ?, 1, 0) AS is_feed_owner,
                  (SELECT COUNT(*) FROM comments c WHERE c.feed_id = F.id) AS comment_count,
                  (SELECT COUNT(*) FROM likes l WHERE l.feed_id = F.id) AS like_count,
                  (SELECT COUNT(*) FROM feeds fp WHERE fp.parent_id = F.id) AS repost_count,
                  (SELECT COUNT(*) FROM tips t WHERE t.feed_id = F.id) AS tip_count,
                  (SELECT name FROM stages stag WHERE u.stage = stag.id  LIMIT 1) AS stage_name,
                  u.id AS user_id, u.username, u.firstname, u.lastname, u.avatar, u.stage, u.country,
                  cs.id AS comment_id,
                  IF(cs.user_id = ?, 1, 0) AS is_comment_owner
                  FROM feeds F
                  JOIN users u ON F.user_id = u.id
                  LEFT JOIN comments cs ON cs.feed_id = F.id
                  ORDER BY F.id DESC LIMIT ?, ?";
        $smt = $this->conn->prepare($query);
        $smt->bind_param("iiii", $user_id, $user_id, $offset, $limit);
    } else {
        $query = "SELECT 
                  F.*, 
                  IF(F.user_id = ?, 1, 0) AS is_feed_owner,
                  (SELECT COUNT(*) FROM comments c WHERE c.feed_id = F.id) AS comment_count,
                  (SELECT COUNT(*) FROM likes l WHERE l.feed_id = F.id) AS like_count,
                  (SELECT COUNT(*) FROM feeds fp WHERE fp.parent_id = F.id) AS repost_count,
                  (SELECT COUNT(*) FROM tips t WHERE t.feed_id = F.id) AS tip_count,
                  (SELECT name FROM stages stag WHERE u.stage = stag.id  LIMIT 1) AS stage_name,
                  u.id AS user_id, u.username, u.firstname, u.lastname, u.avatar, u.stage, u.country
                  FROM feeds F
                  JOIN users u ON F.user_id = u.id
                  WHERE F.id = ? LIMIT 1";
        $smt = $this->conn->prepare($query);
        $smt->bind_param("ii", $user_id, $type);
    }

    $smt->execute();
    $result = $smt->get_result();
    $feeds = [];
    $feeds['isLogin'] = $isUser['success'];
    $feeds['user'] = $isUser['success'] ? $isUser['data'] : [];
    $feeds['success']=true;
    $feeds['feeds'] = [];
    while($rows = $result->fetch_assoc()){
        $feeder=[];
        $feeder['id'] = $rows['id'];
        $feeder['stage_name'] = $rows['stage_name'];
        $feeder['user_id'] = $rows['user_id'];
        $feeder['parent_id'] = $rows['parent_id'];
        $feeder['content'] = $rows['content'];
        $feeder['image'] = json_decode($rows['image'], true) ?? [];
        $feeder['video'] = $rows['video'];
        $feeder['audio'] = $rows['audio'];
        $feeder['created_at'] = $rows['created_at'];
        $feeder['updated_at'] = $rows['updated_at'];
        $feeder['is_feed_owner'] = $rows['is_feed_owner'];
        $feeder['comment_count'] = $rows['comment_count'];
        $feeder['like_count'] = $rows['like_count'];
        $feeder['repost_count'] = $rows['repost_count'];
        $feeder['tip_count'] = $rows['tip_count'];
        $feeder['username'] = $rows['username'];
        $feeder['firstname'] = $rows['firstname'];
        $feeder['lastname'] = $rows['lastname'];
        $feeder['avatar'] = $rows['avatar'];
        $feeder['stage'] = $rows['stage'];
        $feeder['country'] = $rows['country'];
        $feeder['comment_id'] = $rows['comment_id'];
        $feeder['is_comment_owner'] = $rows['is_comment_owner'];
        $feeder['comments'] = $type == "all" ? $this->fetchComment(['user_id' => $rows['user_id'],'type' => $rows['comment_id'] ?? 'all','offset' => null,'limit' => null,'post' => $rows['id']]) ?? [] : $this->fetchComment(['user_id' => $rows['user_id'], 'type' => 'all', 'offset' => 0, 'limit' => 50,'post' => $rows['id']]) ?? [];
        $feeds['feeds'][]=$feeder;
}

return $feeds;
}



public function fetchComment($data) {
    $user_id = explode('-', $data['user_id'])[1] ?? 0;
    $type = $data['type'];
    $offset = $data['offset'] ?? 0;
    $limit = $data['limit'] ?? 10;
    $post_id = $data['post'];

    if ($type == "all") {
        $query = "SELECT 
                  c.*,
                  IF(c.user_id = ?, 1, 0) AS is_comment_owner,
                  (SELECT COUNT(*) FROM likes l WHERE l.comment_id = c.id) AS like_count,
                  (SELECT COUNT(*) FROM comments c_reply WHERE c_reply.parent_id = c.id) AS reply_count,
                  u.id AS user_id, u.username, u.firstname, u.lastname, u.avatar, u.stage, u.country
                  FROM comments c
                  JOIN users u ON c.user_id = u.id
                  WHERE c.feed_id = ?
                  ORDER BY c.id DESC LIMIT ?, ?";
        $smt = $this->conn->prepare($query);
        $smt->bind_param("iiii", $user_id, $post_id, $offset, $limit);
    } else {
        $query = "SELECT 
                  c.*,
                  IF(c.user_id = ?, 1, 0) AS is_comment_owner,
                  (SELECT COUNT(*) FROM likes l WHERE l.comment_id = c.id) AS like_count,
                  (SELECT COUNT(*) FROM comments c_reply WHERE c_reply.parent_id = c.id) AS reply_count,
                  u.id AS user_id, u.username, u.firstname, u.lastname, u.avatar, u.stage, u.country
                  FROM comments c
                  JOIN users u ON c.user_id = u.id
                  WHERE c.id = ? LIMIT 1";
        $smt = $this->conn->prepare($query);
        $smt->bind_param("ii", $user_id, $type);
    }

    $smt->execute();
    $result = $smt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);

    return $rows;
}

public function comments($data){
    $user_id  = explode('-', $data['user'])[1] ?? 0;
    $feed_id  = $data['post'] ?? '';
    $content  = $data['comment'] ?? '';

    // Auth check
    $auth    = new auth($this->conn);
    $islogin = $auth->userProfile($data['user'])['success'] ?? false;
    if (!$islogin) {
        return ['success' => false, 'isLogin' => $islogin, 'message' => 'Unauthorized'];
    }

    // Validate
    if (empty($content) && empty($image) && empty($audio) && empty($video)) {
        return ['success' => false, 'isLogin' => $islogin, 'message' => 'Comment or media required'];
    }

    // Handle files
    $image = '';
    $audio = '';
    $video = '';

    if (!empty($_FILES['image']['name'])) {
        $image = md5(time() . $data['user']).'_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . "../uploads/" . $image);
    }

    if (!empty($_FILES['audio']['name'])) {
        $audio =  md5(time() . $data['user']) . '_' . basename($_FILES['audio']['name']);
        move_uploaded_file($_FILES['audio']['tmp_name'], __DIR__ ."../uploads/" . $audio);
    }

    if (!empty($_FILES['video']['name'])) {
        $video =  md5(time() . $data['user']) . '_' . basename($_FILES['video']['name']);
        move_uploaded_file($_FILES['video']['tmp_name'], __DIR__ . "../uploads/" . $video);
    }

    // Escape values
    $feed_id = $this->conn->real_escape_string($feed_id);
    $user_id = $this->conn->real_escape_string($user_id);
    $content = $this->conn->real_escape_string($content);

    // Insert
    $sql = "INSERT INTO comments (feed_id, user_id, content, image, audio, video) 
            VALUES ('$feed_id', '$user_id', '$content', '$image', '$audio', '$video')";

    $ok = $this->conn->query($sql);

    return [
        'success' => $ok ? true : false,
        'isLogin' => $islogin,
        'message' => $ok ? 'Comment added successfully' : 'Failed to add comment'
    ];
}

public function replycomments($data){
    
}

public function deleteComment($comment_id, $user_id){
    $comment_id = $this->conn->real_escape_string($comment_id);
    $user_id    = $this->conn->real_escape_string($user_id);

    // Step 1: Fetch the comment
    $sql = "SELECT user_id, image, audio, video FROM comments WHERE id = '$comment_id'";
    $result = $this->conn->query($sql);

    if (!$result || $result->num_rows === 0) {
        return ['success' => false, 'message' => 'Comment not found'];
    }

    $row = $result->fetch_assoc();

    // Step 2: Authorization check
    if ($row['user_id'] != $user_id) {
        return ['success' => false, 'message' => 'Not authorized to delete this comment'];
    }

    // Step 3: Remove files if they exist
    $uploadDir = realpath(__DIR__ . "/../uploads") . DIRECTORY_SEPARATOR;

    foreach (['image', 'audio', 'video'] as $field) {
        if (!empty($row[$field])) {
            $filePath = $uploadDir . $row[$field];
            if (file_exists($filePath) && strpos(realpath($filePath), $uploadDir) === 0) {
                unlink($filePath);
            }
        }
    }

    // Step 4: Delete the comment
    $deleteSql = "DELETE FROM comments WHERE id = '$comment_id'";
    $ok = $this->conn->query($deleteSql);

    return [
        'success' => $ok ? true : false,
        'message' => $ok ? 'Comment deleted successfully' : 'Failed to delete comment'
    ];
}





public function deleteFeeds($data) {
    $user_id = explode('-', $data['user'])[1] ?? 0;
    $feed_id = $data['post'] ?? 0;
    $type = $data['type'] ?? 'feed';
    $reason = $data['reason'] ?? 'No reason provided';
    $islogin = (new auth($this->conn))->userProfile($user_id)['success'] ?? false;
    if (!$islogin)  return ['success' => false, 'isLogin' => $islogin, 'message' => 'User not authenticated'];
    if ($feed_id === 0) return ['success' => false, 'isLogin' => $islogin, 'message' => 'Feed ID is required'];

    // Check if the feed belongs to the user
    $check = $this->conn->prepare("SELECT * FROM feeds WHERE id=? AND user_id=?");
    $check->bind_param("ii", $feed_id, $user_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        // Feed exists and belongs to the user, proceed to delete
        $delete = $this->conn->prepare("DELETE FROM feeds WHERE id=?");
        $delete->bind_param("i", $feed_id);
        if ($delete->execute()) {
            return ['success' => true, 'isLogin' => $islogin, 'message' => 'Feed deleted successfully'];
        }
         else {
            return ['success' => false, 'isLogin' => $islogin, 'message' => 'Failed to delete feed'];
        }
    }
    $insertIntoDeleted = $this->conn->prepare("INSERT INTO remove_post (type, user_id, data_id, reason) VALUES (?, ?, ?, ?)");
    $insertIntoDeleted->bind_param("siss", $type, $user_id, $feed_id, $reason);
    $insertIntoDeleted->execute();
    if ($insertIntoDeleted->affected_rows > 0) {
        return ['success' => false, 'isLogin' => $islogin, 'message' => 'Feed not found or does not belong to user, but report has been logged'];
    } else {
        return ['success' => false, 'isLogin' => $islogin, 'message' => 'Failed to log the report for the feed'];
    }
}


public function likeFeeds($data){
    $post_id=$data['post'] ?? 0;
    $user_id=explode('-',$data['user'])[1] ?? 0;
    $auth = new auth($this->conn);
    $islogin = $auth->userProfile($data['user'])['success'] ?? false;
    if (!$islogin) return ['success' => false, 'isLogin' => $islogin, 'message' => 'Unauthorized'];
    if($post_id === 0 || $user_id === 0) return ['success' => false, 'isLogin' => $islogin, 'message' => 'Post ID is required'];
    $smt=$this->conn->prepare("SELECT * FROM likes WHERE feed_id=? AND user_id=?");
    $smt->bind_param("ii",$post_id,$user_id);
    $smt->execute();
    $result=$smt->get_result();
    if($result->num_rows > 0){
        // User has already liked the post, so we will unlike it
        $delete=$this->conn->prepare("DELETE FROM likes WHERE feed_id=? AND user_id=?");
        $delete->bind_param("ii",$post_id,$user_id);
        $delete->execute();
        $smtupdate=$this->conn->prepare("INSERT INTO current_update SET (type, post_id) values ('likefeed',?)");
        $smtupdate->bind_param("i", $post_id);
        $smtupdate->execute();
        return ['success' => true, 'isliked' => false, 'message' => 'Post unliked'];
    } else {
        // User has not liked the post, so we will like it
        $insert=$this->conn->prepare("INSERT INTO likes (feed_id, user_id) VALUES (?, ?)");
        $insert->bind_param("ii",$post_id,$user_id);
        $insert->execute();
         $smtupdate=$this->conn->prepare("INSERT INTO current_update SET (type, post_id) values ('likefeed',?)");
        $smtupdate->bind_param("i", $post_id);
        $smtupdate->execute();
        return ['success' => true, 'isliked' => true, 'message' => 'Post liked'];
}


}



   }
