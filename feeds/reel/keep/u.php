<?php

$uploadDir = "uploads/";

if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if (isset($_FILES['video'])) {

    $fileTmp = $_FILES['video']['tmp_name'];
    $fileName = time() . ".webm";
    $targetPath = $uploadDir . $fileName;

    if (move_uploaded_file($fileTmp, $targetPath)) {
        echo "15 second video saved!";
    } else {
        echo "Upload failed.";
    }

} else {
    echo "No video received.";
}
