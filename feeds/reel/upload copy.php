<?php
include __DIR__."/../../main-function.php";

class ReelUpload extends Main {

    public function __construct($conn) {
        parent::__construct($conn);
    }

    public function upload() {

        session_start();
        $user_id = $_SESSION['user_id'] ?? 1;

        if (!isset($_FILES['video'])) {
            $this->json(false, "Video missing");
        }

        $video = $_FILES['video'];

        if ($video['error'] !== 0) {
            $this->json(false, "Upload error");
        }

        if (!is_dir("uploads/reels")) {
            mkdir("uploads/reels", 0777, true);
        }

        $fileName = uniqid("reel_", true) . ".webm";
        $path = "uploads/reels/" . $fileName;

        if (!move_uploaded_file($video['tmp_name'], $path)) {
            $this->json(false, "Failed saving video");
        }

        $data = [
            "video" => $path,
            "duration" => 5,
            "type" => "reel"
        ];

        $jsonData = json_encode($data);

        $stmt = $this->conn->prepare("
            INSERT INTO feeds (user_id, category, txt, description, keywords, subscription, data, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $user_id,
            "reel",
            $_POST['title'] ?? '',
            $_POST['description'] ?? '',
            "",
            "free",
            $jsonData,
            "reel"
        ]);

        $this->json(true, "Uploaded successfully");
    }

    private function json($success, $message) {
        header("Content-Type: application/json");
        echo json_encode([
            "success" => $success,
            "message" => $message
        ]);
        exit;
    }
}

$upload = new ReelUpload($conn);
$upload->upload();
