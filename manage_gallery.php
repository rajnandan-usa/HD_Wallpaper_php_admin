<?php
include("includes/header.php");
// require("includes/connection.php");
require("includes/function.php");

$kwallpaper = new k_wallpaper;

// Set table name and pagination variables
$tableName = "tbl_gallery";
$targetpage = "manage_gallery.php";
$limit = 15;

// Get total pages
$query = "SELECT COUNT(*) as num FROM $tableName";
$result = $cn->query($query);
$row = $result->fetch_assoc();
$total_pages = $row['num'];

$stages = 3;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start = ($page - 1) * $limit;

// Calculate the last page
$lastpage = ceil($total_pages / $limit);

// Get page data
$qry = "SELECT tbl_gallery.*, tbl_category.category_name 
        FROM tbl_gallery
        LEFT JOIN tbl_category ON tbl_gallery.cat_id = tbl_category.cid 
        ORDER BY tbl_gallery.id DESC 
        LIMIT ?, ?";
$stmt = $cn->prepare($qry);
$stmt->bind_param("ii", $start, $limit);
$stmt->execute();
$result = $stmt->get_result();

if (isset($_GET['img_id'])) {
    $kwallpaper->deleteImage();
    echo "<script>document.location='manage_gallery.php';</script>";
    exit;
}
?>

<!-- h2 stays for breadcrumbs -->
<h2><a href="home.php">Dashboard</a> &raquo; <a href="#" class="active">Manage Gallery</a></h2>

<!-- Main Content -->
<div id="main">
    <form action="" class="jNice">
        <h3>Manage Gallery</h3>
        <h3 align="right"><a href="add_gallery_image.php?add=yes">Add Image</a></h3>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <td><h3>Category</h3></td>
                <td><h3>Image</h3></td>
                <td><h3>Edit</h3></td>
                <td><h3>Delete</h3></td>
            </tr>
            <?php
            $i = 0;
            while ($row = $result->fetch_assoc()) {
            ?>
                <tr <?php if ($i % 2 == 0) { ?>class="odd"<?php } ?>>
                    <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                    <td><img src="categories/<?php echo htmlspecialchars($row['category_name']); ?>/<?php echo htmlspecialchars($row['image']); ?>" height="100" width="100" /></td>
                    <td class="action"><a href="edit_gallery_image.php?img_id=<?php echo $row['id']; ?>" class="edit">Edit</a></td>
                    <td class="action"><a href="?img_id=<?php echo $row['id']; ?>" class="delete" onclick="return confirm('Are you sure you want to delete this Image?');">Delete</a></td>
                </tr>
            <?php
                $i++;
            }
            ?>
        </table><br />

        <?php
        // Pagination
        $paginate = '';
        if ($lastpage > 1) {
            $paginate .= "<div class='paginate'>";
            // Previous
            $prev = $page - 1;
            if ($page > 1) {
                $paginate .= "<a href='$targetpage?page=$prev'>previous</a>";
            } else {
                $paginate .= "<span class='disabled'>previous</span>";
            }

            // Pages
            if ($lastpage < 7 + ($stages * 2)) {
                for ($counter = 1; $counter <= $lastpage; $counter++) {
                    if ($counter == $page) {
                        $paginate .= "<span class='current'>$counter</span>";
                    } else {
                        $paginate .= "<a href='$targetpage?page=$counter'>$counter</a>";
                    }
                }
            } elseif ($lastpage > 5 + ($stages * 2)) {
                if ($page < 1 + ($stages * 2)) {
                    for ($counter = 1; $counter < 4 + ($stages * 2); $counter++) {
                        if ($counter == $page) {
                            $paginate .= "<span class='current'>$counter</span>";
                        } else {
                            $paginate .= "<a href='$targetpage?page=$counter'>$counter</a>";
                        }
                    }
                    $paginate .= "...";
                    $paginate .= "<a href='$targetpage?page=$lastpage-1'>$lastpage-1</a>";
                    $paginate .= "<a href='$targetpage?page=$lastpage'>$lastpage</a>";
                } elseif ($lastpage - ($stages * 2) > $page && $page > ($stages * 2)) {
                    $paginate .= "<a href='$targetpage?page=1'>1</a>";
                    $paginate .= "<a href='$targetpage?page=2'>2</a>";
                    $paginate .= "...";
                    for ($counter = $page - $stages; $counter <= $page + $stages; $counter++) {
                        if ($counter == $page) {
                            $paginate .= "<span class='current'>$counter</span>";
                        } else {
                            $paginate .= "<a href='$targetpage?page=$counter'>$counter</a>";
                        }
                    }
                    $paginate .= "...";
                    $paginate .= "<a href='$targetpage?page=$lastpage-1'>$lastpage-1</a>";
                    $paginate .= "<a href='$targetpage?page=$lastpage'>$lastpage</a>";
                } else {
                    $paginate .= "<a href='$targetpage?page=1'>1</a>";
                    $paginate .= "<a href='$targetpage?page=2'>2</a>";
                    $paginate .= "...";
                    for ($counter = $lastpage - (2 + ($stages * 2)); $counter <= $lastpage; $counter++) {
                        if ($counter == $page) {
                            $paginate .= "<span class='current'>$counter</span>";
                        } else {
                            $paginate .= "<a href='$targetpage?page=$counter'>$counter</a>";
                        }
                    }
                }
            }

            // Next
            $next = $page + 1;
            if ($page < $lastpage) {
                $paginate .= "<a href='$targetpage?page=$next'>next</a>";
            } else {
                $paginate .= "<span class='disabled'>next</span>";
            }

            $paginate .= "</div>";
        }

        echo $paginate;
        ?>
        <br />
    </form>
</div>
<!-- // #main -->

<div class="clear"></div>
</div>
<!-- // #container -->
</div>
<!-- // #containerHolder -->

<?php include("includes/footer.php"); ?>
