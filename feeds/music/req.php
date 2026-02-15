<?php
include __DIR__."/../../main-function.php";

class music extends Main {

    public function __construct($conn) { 
        parent::__construct($conn); 
    }

    public function upload() {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            exit("Invalid request");
        }

        session_start();
        $user_id = $_SESSION['user_id'] ?? 1; // change to your auth system

        if (!isset($_FILES['audio'])) {
            exit("Audio file missing");
        }

        // ===== CREATE DIRECTORIES =====
        if (!is_dir("uploads/audio")) {
            mkdir("uploads/audio", 0777, true);
        }

        if (!is_dir("uploads/covers")) {
            mkdir("uploads/covers", 0777, true);
        }

        // ===== AUDIO =====
        $audio = $_FILES['audio'];
        $audioName = uniqid() . "_" . basename($audio['name']);
        $audioPath = "uploads/audio/" . $audioName;

        move_uploaded_file($audio['tmp_name'], $audioPath);

        // ===== COVER =====
        $coverPath = null;

        if (!empty($_FILES['cover']['tmp_name'])) {
            $cover = $_FILES['cover'];
            $coverName = uniqid() . "_" . basename($cover['name']);
            $coverPath = "uploads/covers/" . $coverName;
            move_uploaded_file($cover['tmp_name'], $coverPath);
        }

        // ===== JSON DATA =====
        $data = [
            "title" => $_POST['title'] ?? '',
            "description" => $_POST['description'] ?? '',
            "hashtags" => $_POST['hashtags'] ?? '',
            "category" => $_POST['category'] ?? '',
            "premium" => $_POST['premium'] ?? 0,
            "audio" => $audioPath,
            "cover" => $coverPath
        ];

        $jsonData = json_encode($data);

        // ===== INSERT INTO feeds =====
        $stmt = $this->conn->prepare(
            "INSERT INTO feeds (user_id, data, status) VALUES (?, ?, ?)"
        );

        $stmt->execute([$user_id, $jsonData, "feed"]);

        echo "success";
    }
}


// ===== CALL CLASS =====
$music = new music($conn);
$music->upload();
