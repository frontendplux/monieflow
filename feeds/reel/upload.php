<?php

if(isset($_FILES['video'])) {

    $file = $_FILES['video'];
    $target = "uploads/" . time() . ".mp4";

    if(move_uploaded_file($file['tmp_name'], $target)) {
        echo "Saved Successfully";
    } else {
        echo "Upload Failed";
    }
}
?>
