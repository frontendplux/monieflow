<?php
include __DIR__."/../../main-function.php";

class music extends Main {

    public function __construct($conn) { 
        parent::__construct($conn); 
    }

    public function upload() {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(false, "Invalid request");
        }

        session_start();
        $user_id = $_SESSION['user_id'] ?? 1;

        if (!isset($_FILES['audio']) || $_FILES['audio']['error'] !== 0) {
            $this->jsonResponse(false, "Audio file missing");
        }

        // ===== VALIDATE AUDIO =====
        $audio = $_FILES['audio'];

        $allowedAudio = ['audio/mpeg', 'audio/mp3', 'audio/wav'];
        if (!in_array($audio['type'], $allowedAudio)) {
            $this->jsonResponse(false, "Only MP3/WAV allowed");
        }

        if ($audio['size'] > 20 * 1024 * 1024) { // 20MB
            $this->jsonResponse(false, "Audio too large (Max 20MB)");
        }

        // ===== CREATE DIRECTORIES =====
        if (!is_dir("uploads/audio")) {
            mkdir("uploads/audio", 0777, true);
        }

        if (!is_dir("uploads/covers")) {
            mkdir("uploads/covers", 0777, true);
        }

        // ===== SAVE AUDIO =====
        $audioExt = pathinfo($audio['name'], PATHINFO_EXTENSION);
        $audioName = uniqid("audio_", true) . "." . $audioExt;
        $audioPath = "uploads/audio/" . $audioName;

        if (!move_uploaded_file($audio['tmp_name'], $audioPath)) {
            $this->jsonResponse(false, "Failed to upload audio");
        }

        // ===== SAVE COVER =====
        $coverPath = null;

        if (!empty($_FILES['cover']['tmp_name'])) {

            $cover = $_FILES['cover'];

            if ($cover['error'] === 0) {

                $allowedImages = ['image/jpeg', 'image/png', 'image/webp'];

                if (in_array($cover['type'], $allowedImages)) {

                    $coverExt = pathinfo($cover['name'], PATHINFO_EXTENSION);
                    $coverName = uniqid("cover_", true) . "." . $coverExt;
                    $coverPath = "uploads/covers/" . $coverName;

                    move_uploaded_file($cover['tmp_name'], $coverPath);
                }
            }
        }

        // ===== CLEAN INPUTS =====
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $hashtags = trim($_POST['hashtags'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $premium = isset($_POST['premium']) ? 1 : 0;

        if (empty($title)) {
            $this->jsonResponse(false, "Title required");
        }

        // ===== PREPARE JSON =====
        $data = [
            "title" => htmlspecialchars($title, ENT_QUOTES),
            "description" => htmlspecialchars($description, ENT_QUOTES),
            "hashtags" => htmlspecialchars($hashtags, ENT_QUOTES),
            "category" => htmlspecialchars($category, ENT_QUOTES),
            "premium" => $premium,
            "audio" => $audioPath,
            "cover" => $coverPath
        ];

        $jsonData = json_encode($data);

        // ===== INSERT INTO DATABASE =====
        $stmt = $this->conn->prepare(
            "INSERT INTO feeds (user_id, data, status) VALUES (?, ?, ?)"
        );

        $stmt->execute([$user_id, $jsonData, "music"]);

        $this->jsonResponse(true, "Music uploaded successfully");
    }

    private function jsonResponse($success, $message) {
        header('Content-Type: application/json');
        echo json_encode([
            "success" => $success,
            "message" => $message
        ]);
        exit;
    }
}


// ===== RUN =====
$music = new music($conn);
$music->upload();
