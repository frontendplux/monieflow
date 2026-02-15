<?php

$uploadDir = "uploads/";

if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if (isset($_FILES['video'])) {

    $fileName = time() . "_" . $_FILES['video']['name'];
    $targetPath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['video']['tmp_name'], $targetPath)) {
        echo "Video saved successfully!";
    } else {
        echo "Error saving video.";
    }

} else {
    echo "No video received.";
}

?>
