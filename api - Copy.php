<?php
include("includes/connection.php");

// Ensure you're using the mysqli or PDO extension instead of the deprecated mysql extension
// For this example, I will use mysqli

// Connect to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$query = "";

if (isset($_GET['cat_id'])) {
    $cat_id = intval($_GET['cat_id']); // Use intval to prevent SQL injection

    $query = "SELECT tbl_gallery.id, tbl_gallery.cat_id, tbl_gallery.image, tbl_category.category_name 
              FROM tbl_gallery
              LEFT JOIN tbl_category ON tbl_gallery.cat_id = tbl_category.cid
              WHERE tbl_gallery.cat_id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $cat_id);
    $stmt->execute();
    $result = $stmt->get_result();
} elseif (isset($_GET['latest'])) {
    $limit = intval($_GET['latest']); // Use intval to prevent SQL injection

    $query = "SELECT tbl_gallery.id, tbl_gallery.cat_id, tbl_gallery.image, tbl_category.category_name 
              FROM tbl_gallery
              LEFT JOIN tbl_category ON tbl_gallery.cat_id = tbl_category.cid
              ORDER BY tbl_gallery.id DESC 
              LIMIT ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $query = "SELECT cid, category_name FROM tbl_category";
    
    $result = $conn->query($query);
}

$set = array();

if ($result->num_rows > 0) {
    while ($link = $result->fetch_assoc()) {
        $set['wallpaper'][] = $link;
    }
}

header('Content-Type: application/json');
echo json_encode($set);

if (isset($stmt)) {
    $stmt->close();
}

$conn->close();
?>
