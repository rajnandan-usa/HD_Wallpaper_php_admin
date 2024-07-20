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

if (isset($_GET['cat_id'])) {
    $cat_id = intval($_GET['cat_id']); // Use intval to prevent SQL injection

    $stmt = $conn->prepare("SELECT * FROM tbl_category WHERE cid = ?");
    $stmt->bind_param("i", $cat_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $cat_img_row = $result->fetch_assoc();

    $files = array();
    $dir = opendir(dirname(realpath(__FILE__)) . '/categories/' . $cat_img_row['category_name'] . '/');
    
    while (($file = readdir($dir)) !== false) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        $files[] = "image:" . $file;
    }
    closedir($dir);

    header('Content-type: application/json');
    echo json_encode(array('HDwallpaper' => $files));

    $stmt->close();
} elseif (isset($_GET['latest'])) {
    $limit = intval($_GET['latest']); // Use intval to prevent SQL injection

    $query = "SELECT tbl_gallery.image FROM tbl_gallery
              LEFT JOIN tbl_category ON tbl_gallery.cat_id = tbl_category.cid 
              ORDER BY tbl_gallery.id DESC LIMIT ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();

    $set = array();
    while ($link = $result->fetch_assoc()) {
        $set['HDwallpaper'][] = $link;
    }

    header('Content-type: application/json');
    echo json_encode($set);

    $stmt->close();
} else {
    $query = "SELECT cid, category_name FROM tbl_category";
    
    $result = $conn->query($query);

    $set = array();
    while ($link = $result->fetch_assoc()) {
        $set['HDwallpaper'][] = $link;
    }

    header('Content-type: application/json');
    echo json_encode($set);
}

$conn->close();
?>
