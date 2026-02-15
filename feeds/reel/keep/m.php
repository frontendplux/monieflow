<?php

$uploadDir = "uploads/";
if(!file_exists($uploadDir)){
    mkdir($uploadDir,0777,true);
}

$videoName = time()."_video.webm";
$musicName = time()."_music.mp3";

$videoPath = $uploadDir.$videoName;
$musicPath = $uploadDir.$musicName;

move_uploaded_file($_FILES['video']['tmp_name'],$videoPath);
move_uploaded_file($_FILES['music']['tmp_name'],$musicPath);

$outputVideo = $uploadDir.time()."_final.mp4";

/*
Merge video + music
- Shortest ensures it stops at 15 seconds
*/
$cmd = "ffmpeg -i $videoPath -i $musicPath -map 0:v -map 1:a -c:v copy -shortest $outputVideo";

exec($cmd,$out,$status);

if($status === 0){
    echo "Video merged successfully!";
}else{
    echo "Merging failed.";
}
?>
