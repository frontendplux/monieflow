<?php
// req.php

header('Content-Type: application/json; charset=utf-8');

// Make sure no output happens before headers
ob_start();

include __DIR__ . "/../conn/conn.php";

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ────────────────────────────────────────────────
// Basic security / auth check
if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Not authenticated. Please log in.'
    ]);
    exit;
}

$user_id = (int) $_SESSION['user_id'];

// ────────────────────────────────────────────────
// Only allow POST requests for now
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit;
}

// ────────────────────────────────────────────────
// Get action
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'feeds':
        // ────────────────────────────────
        // 1. Get post content
        $content = trim($_POST['content'] ?? '');

        if (strlen($content) < 1) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Post content is required'
            ]);
            exit;
        }

        if (strlen($content) > 280) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Post content cannot exceed 280 characters'
            ]);
            exit;
        }

        // ────────────────────────────────
        // 2. Prepare upload directory
        $upload_dir       = __DIR__ . "/../uploads/posts/";
        $public_base_path = "/uploads/posts/";   // what gets saved in DB

        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0755, true)) {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to create upload directory'
                ]);
                exit;
            }
        }

        // Make sure directory is writable
        if (!is_writable($upload_dir)) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Upload directory is not writable'
            ]);
            exit;
        }

        // ────────────────────────────────
        // 3. Handle image uploads
        $images = [];
        $max_images = 4;
        $max_size   = 5 * 1024 * 1024; // 5MB
        $allowed    = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

        if (!empty($_FILES['images']['name']) && is_array($_FILES['images']['name'])) {
            $count = count($_FILES['images']['name']);

            for ($i = 0; $i < $count && count($images) < $max_images; $i++) {
                $file     = $_FILES['images'];
                $tmp_name = $file['tmp_name'][$i] ?? '';
                $error    = $file['error'][$i]    ?? UPLOAD_ERR_NO_FILE;
                $size     = $file['size'][$i]     ?? 0;
                $type     = $file['type'][$i]     ?? '';
                $name     = $file['name'][$i]     ?? '';

                if ($error !== UPLOAD_ERR_OK || $size === 0) {
                    continue;
                }

                if ($size > $max_size) {
                    continue; // skip oversized (you can collect error message if desired)
                }

                if (!in_array($type, $allowed, true)) {
                    continue;
                }

                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                if (!in_array($ext, ['jpg','jpeg','png','webp','gif'])) {
                    continue;
                }

                // Generate safe unique filename
                $filename = 'post_' . time() . '_' . bin2hex(random_bytes(5)) . '.' . $ext;
                $destination = $upload_dir . $filename;

                if (move_uploaded_file($tmp_name, $destination)) {
                    $images[] = $filename; // only filename → prepend $public_base_path when displaying
                }
            }
        }

        $images_json = !empty($images) ? json_encode($images) : null;

        // ────────────────────────────────
        // 4. Insert into database
        $stmt = $conn->prepare("
            INSERT INTO feeds 
            (user_id, text, images, created_at)
            VALUES (?, ?, ?, NOW())
        ");

        if (!$stmt) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Database prepare error: ' . $conn->error
            ]);
            exit;
        }

        $stmt->bind_param("iss", $user_id, $content, $images_json);

        if (!$stmt->execute()) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to save post: ' . $stmt->error
            ]);
            $stmt->close();
            exit;
        }

        $post_id = $conn->insert_id;
        $stmt->close();

        // ────────────────────────────────
        // 5. Success
        echo json_encode([
            'success' => true,
            'message' => 'Post created successfully',
            'post_id' => $post_id
        ]);

        break;

    default:
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid or missing action'
        ]);
        break;
}

// Optional: clean output buffer if needed
ob_end_flush();