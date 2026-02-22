<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Content-Type: application/json");

include __DIR__."/../../main-function.php";

class ReelUpload extends Main {

    public function __construct($conn) {
        parent::__construct($conn);
    }

    public function upload() {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(["success"=>false,"message"=>"Invalid request"]);
            exit;
        }

        
        $user_id =$this->getUserData()['data']['id'];

        if (!isset($_FILES['video'])) {
            echo json_encode(["success"=>false,"message"=>"No video file received"]);
            exit;
        }

        if (!is_dir(__DIR__."/../../uploads/reels")) {
            mkdir(__DIR__."/../../uploads/reels", 0777, true);
        }

        $video = $_FILES['video'];

        if ($video['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(["success"=>false,"message"=>"Upload error"]);
            exit;
        }

        $extension = pathinfo($video['name'], PATHINFO_EXTENSION);
        $allowedExt = ['webm','mp4'];

        if (!in_array(strtolower($extension), $allowedExt)) {
            echo json_encode(["success"=>false,"message"=>"Invalid video format"]);
            exit;
        }

        $videoName = uniqid() . "." . $extension;
        $videoPath = __DIR__."/../../uploads/reels/" . $videoName;

        if (!move_uploaded_file($video['tmp_name'], $videoPath)) {
            echo json_encode(["success"=>false,"message"=>"Move failed"]);
            exit;
        }





        /* ============================
   HANDLE REEL COVER IMAGE
============================ */
$coverName = null;

if (isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {

    if (!is_dir(__DIR__."/../../uploads/reel_covers")) {
        mkdir(__DIR__."/../../uploads/reel_covers", 0777, true);
    }

    $coverExt = pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION);
    $allowedImageExt = ['jpg','jpeg','png'];

    if (in_array(strtolower($coverExt), $allowedImageExt)) {

        $coverName = uniqid() . "." . $coverExt;
        $coverPath = __DIR__."/../../uploads/reel_covers/" . $coverName;

        move_uploaded_file($_FILES['cover']['tmp_name'], $coverPath);
    }
}




        // $data = [
        //     "title" => $_POST['title'] ?? '',
        //     "description" => $_POST['description'] ?? '',
        //     "video" => $videoName,
        //     "created_at" => date("Y-m-d H:i:s")
        // ];

$data = [
    "title" => $_POST['title'] ?? '',
    "description" => $_POST['description'] ?? '',
    "video" => $videoName,
    "cover" => $coverName,
    "created_at" => date("Y-m-d H:i:s")
];




        $jsonData = json_encode($data);

        $stmt = $this->conn->prepare(
            "INSERT INTO feeds (user_id, category, txt, description,  data, status) VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$user_id, $_POST['ids'], $_POST['title'], $_POST['description'],  $jsonData, "reel"]);

        echo json_encode([
            "success" => true,
            "message" => "Reel uploaded successfully"
        ]);
        exit;
    }
}

$upload = new ReelUpload($conn);
$upload->upload();
