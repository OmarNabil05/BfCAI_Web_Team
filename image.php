<?php
// Image serving endpoint - serves images stored in database as BLOBs
require_once 'config/db.php';

// Get image ID from query parameter
$image_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($image_id <= 0) {
    http_response_code(400);
    exit('Invalid image ID');
}

// Fetch image from database
$stmt = $conn->prepare("SELECT mime_type, data FROM images WHERE id = ?");
$stmt->bind_param("i", $image_id);
$stmt->execute();
$stmt->bind_result($mime_type, $image_data);

if ($stmt->fetch()) {
    // Set appropriate headers
    header("Content-Type: " . $mime_type);
    header("Cache-Control: public, max-age=31536000"); // Cache for 1 year
    header("Content-Length: " . strlen($image_data));
    
    // Output image data
    echo $image_data;
} else {
    // Image not found
    http_response_code(404);
    exit('Image not found');
}

$stmt->close();
$conn->close();
?>
