<?php
include("includes/connection.php");

// Ensure you're using the mysqli or PDO extension instead of the deprecated mysql extension
// For this example, I will use mysqli

if (isset($_GET['cat_id'])) {
    $cat_id = intval($_GET['cat_id']); // Use intval to prevent SQL injection

    $conn = new mysqli($servername, $username, $password, $dbname); // Assuming connection variables are defined in connection.php

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT * FROM tbl_category WHERE cid = ?");
    $stmt->bind_param("i", $cat_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $cat_img_row = $result->fetch_assoc();

    $cat_nm = $cat_img_row['category_name'];
    $files = array();

    $dir = opendir(dirname(realpath(__FILE__)) . '/categories/' . $cat_nm . '/');
    $allimages = array();
    while (($file = readdir($dir)) !== false) {
        if ($file == '.' || $file == '..') {
            continue;
        }
        $allimages[] = $file;
    }
    closedir($dir);

    $total_arr = array_merge([$cat_nm], $allimages);
    sort($total_arr);

    $array = [];
    foreach ($total_arr as $key => $file) {
        if ($key != count($total_arr) - 1) {
            $array['HDwallpaper'][] = array(
                'images' => $file,
                'cat_name' => $cat_nm
            );
        }
    }
    echo json_encode($array);

    $stmt->close();
    $conn->close();
} elseif (isset($_GET['latest'])) {
    $limit = intval($_GET['latest']); // Use intval to prevent SQL injection

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT tbl_gallery.image, tbl_category.category_name FROM tbl_gallery
                            LEFT JOIN tbl_category ON tbl_gallery.cat_id = tbl_category.cid 
                            ORDER BY tbl_gallery.id DESC LIMIT ?");
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();

    $set = array();
    while ($link = $result->fetch_assoc()) {
        $set['HDwallpaper'][] = $link;
    }
    echo json_encode($set);

    $stmt->close();
    $conn->close();
} else {
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $query = "SELECT cid, category_name, category_image FROM tbl_category";
    $result = $conn->query($query);

    $set = array();
    while ($link = $result->fetch_assoc()) {
        $set['HDwallpaper'][] = $link;
    }
    echo json_encode($set);

    $conn->close();
}
?>
