<?php 
include("includes/header.php");
require("includes/function.php");

$kwallpaper = new k_wallpaper;



// Get all categories
$qry = "SELECT * FROM tbl_category";
$result = $cn->query($qry);

// Get image details if img_id is set
if (isset($_GET['img_id'])) {
    $img_id = $cn->real_escape_string($_GET['img_id']);
    $img_qry = "SELECT * FROM tbl_gallery WHERE id='$img_id'";
    $img_res = $cn->query($img_qry);
    $img_row = $img_res->fetch_assoc();
}

// Handle form submission for image editing
if (isset($_POST['submit']) && isset($_GET['img_id'])) {
    $kwallpaper->editimage();
    echo "<script>document.location='manage_gallery.php';</script>"; 
    exit;
}

?>

<!-- Breadcrumbs -->
<h2><a href="home.php">Dashboard</a> &raquo; <a href="#" class="active"></a></h2>

<div id="main">
    <h3>Edit Image</h3>
    <form action="" method="post" class="jNice" enctype="multipart/form-data">
        <fieldset>
            <p>
                <label>Select Category:</label>
                <select name="category">
                    <option value="0">--Select Category--</option>
                    <?php
                    while ($row = $result->fetch_assoc()) {
                        $selected = '';
                        if (isset($_POST['category'])) {
                            if ($_POST['category'] == $row['cid']) {
                                $selected = 'selected="selected"';
                            }
                        } else if ($img_row['cat_id'] == $row['cid']) {
                            $selected = 'selected="selected"';
                        }
                        ?>
                        <option value="<?php echo htmlspecialchars($row['cid']); ?>" <?php echo $selected; ?>>
                            <?php echo htmlspecialchars($row['category_name']); ?>
                        </option>
                        <?php
                    }
                    ?>
                </select>
            </p>

            <p><label>Image:</label>
                <?php 
                if (isset($img_row['cat_id'])) {
                    $cat_img_res = $cn->query("SELECT * FROM tbl_category WHERE cid='" . $img_row['cat_id'] . "'");
                    $cat_img_row = $cat_img_res->fetch_assoc();
                }
                ?>
                <img src="categories/<?php echo htmlspecialchars($cat_img_row['category_name']); ?>/<?php echo htmlspecialchars($img_row['image']); ?>" height="100" width="100" />
            </p>

            <p><label>Select Image:</label>
                <input type="file" name="image" id="image" class="text-long" />
            </p>

            <input type="submit" name="submit" value="Edit Image" />
        </fieldset>
    </form>
</div>
<!-- // #main -->

<div class="clear"></div>
</div>
<!-- // #container -->
</div>    
<!-- // #containerHolder -->

<?php include("includes/footer.php"); ?>
