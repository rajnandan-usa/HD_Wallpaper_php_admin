<?php
include("includes/header.php");
// require("includes/connection.php");
require("includes/function.php");

$kwallpaper = new k_wallpaper;

if (isset($_POST['submit']) && isset($_GET['add'])) {
    $_SESSION['msg'] = "Category added successfully";
    $kwallpaper->addCategory();
    echo "<script>document.location='manage_category.php';</script>";
    exit;
}

if (isset($_GET['cat_id'])) {
    $cat_id = $_GET['cat_id'];
    $qry = "SELECT * FROM tbl_category WHERE cid = ?";
    $stmt = $cn->prepare($qry);
    $stmt->bind_param("i", $cat_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
}

if (isset($_POST['submit']) && isset($_POST['edit'])) {
    $kwallpaper->editCategory();
    echo "<script>document.location='manage_category.php';</script>";
    exit;
}
?>

<script src="js/category.js" type="text/javascript"></script>

<div id="main">
    <h2><a href="home.php">Dashboard</a> &raquo; <a href="#" class="active"></a></h2>

    <form action="" name="addeditcategory" method="post" class="jNice" onsubmit="return checkValidation(this);" enctype="multipart/form-data">
        <input type="hidden" name="edit" value="<?php echo isset($_GET['cat_id']) ? htmlspecialchars($_GET['cat_id']) : ''; ?>" />

        <h3><?php echo isset($_GET['cat_id']) ? 'Edit' : 'Add'; ?> Category</h3>
        <fieldset>
            <p>
                <label>Category Name:</label>
                <input type="text" name="category_name" id="category_name" value="<?php echo isset($row['category_name']) ? htmlspecialchars($row['category_name']) : ''; ?>" class="text-long">
            </p>
            <?php if (isset($row['category_image'])) { ?>
                <img src="images/thumbs/<?php echo htmlspecialchars($row['category_image']); ?>" />
            <?php } ?>
            <p style="margin-top: -35px;">
                <label style="padding-top:40px;">Select Image:</label>
                <input type="file" name="image" id="image" class="text-long" />
            </p>
            <input type="submit" name="submit" value="<?php echo isset($_GET['cat_id']) ? 'Edit Category' : 'Add Category'; ?>" onclick="return chk(this);" />
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
