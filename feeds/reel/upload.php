<?php
include __DIR__."/../../main-function.php";

class ReelUpload extends Main {

    public function __construct($conn) {
        parent::__construct($conn);
    }

    public function upload() {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                "success" => false,
                "message" => "Invalid request"
            ]);
            exit;
        }

        session_start();
        $user_id = $_SESSION['user_id'] ?? 1;

        if (!isset($_FILES['video'])) {
            echo json_encode([
                "success" => false,
                "message" => "No video file"
            ]);
            exit;
        }

        // Create folder if not exists
        if (!is_dir("uploads/reels")) {
            mkdir("uploads/reels", 0777, true);
        }

        $video = $_FILES['video'];

        // Validate type
        $allowed = ['video/webm', 'video/mp4'];
        if (!in_array($video['type'], $allowed)) {
            echo json_encode([
                "success" => false,
                "message" => "Invalid video format"
            ]);
            exit;
        }

        $videoName = uniqid() . ".webm";
        $videoPath = "uploads/reels/" . $videoName;

        if (!move_uploaded_file($video['tmp_name'], $videoPath)) {
            echo json_encode([
                "success" => false,
                "message" => "Upload failed"
            ]);
            exit;
        }

        // Save JSON data
        $data = [
            "title" => $_POST['title'] ?? '',
            "description" => $_POST['description'] ?? '',
            "category" => $_POST['category'] ?? 'reel',
            "video" => $videoName,
            "created_at" => date("Y-m-d H:i:s")
        ];

        $jsonData = json_encode($data);

        $stmt = $this->conn->prepare(
            "INSERT INTO feeds (user_id, data, status) VALUES (?, ?, ?)"
        );

        $stmt->execute([$user_id, $jsonData, "reel"]);

        echo json_encode([
            "success" => true,
            "message" => "Reel uploaded successfully"
        ]);
    }
}

$upload = new ReelUpload($conn);
$upload->upload();
