<?php
// 1. Basic Security & Headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Adjust if running on different domains

// 2. Define Upload Directory
$uploadDir = 'uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// 3. Check if file exists in the request
if (isset($_FILES['video'])) {
    $file = $_FILES['video'];
    
    // Check for PHP upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['status' => 'error', 'message' => 'Upload error code: ' . $file['error']]);
        exit;
    }

    // 4. Validate File Size (e.g., limit to 20MB)
    if ($file['size'] > 20 * 1024 * 1024) {
        echo json_encode(['status' => 'error', 'message' => 'File too large.']);
        exit;
    }

    // 5. Generate a unique filename
    // Note: The JS sends it as .webm
    $filename = 'status_' . time() . '_' . bin2hex(random_bytes(4)) . '.webm';
    $destination = $uploadDir . $filename;

    // 6. Move the file from temp storage to destination
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        echo json_encode([
            'status' => 'success',
            'message' => 'File uploaded successfully',
            'file_path' => $destination
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to move uploaded file.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No video data received.']);
}
?>