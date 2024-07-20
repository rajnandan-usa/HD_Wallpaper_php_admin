<?php 
include("includes/header.php");
require("includes/function.php");

// Initialize the k_wallpaper class
$kwallpaper = new k_wallpaper;



$qry = "SELECT * FROM tbl_category";
$result = $cn->query($qry);

if (isset($_POST['submit']) && isset($_GET['add'])) {
    $kwallpaper->addimage();
    echo "<script>document.location='manage_gallery.php';</script>"; 
    exit;
}

?>

<script src="js/gallery.js" type="text/javascript"></script>
<script type="text/javascript">
var count = 1;
$(function() {
    $('p#add_field').click(function() {
        count += 1;
        if (count <= 5) {
            $('#addcontainer').append(
                '<p><label style="padding-top:40px;">Select Image ' + count + '</label>  <input id="image' + count + '" name="image[]" type="file" class="text-long" /></p>'
            );
        } else {
            alert("Maximum 5 images you can add.");
        }
    });
});
</script> 

<!-- Breadcrumbs -->
<h2><a href="home.php">Dashboard</a> &raquo; <a href="#" class="active"></a></h2>

<div id="main">                 
    <h3>Add Image</h3>                   
    <form action="" method="post" class="jNice" enctype="multipart/form-data" onsubmit="return checkValidation(this);">
        <fieldset>
            <p>
                <label>Select Category:</label>
                <select name="category_id">
                    <option value="0">--Select Category--</option>
                    <?php
                    while ($row = $result->fetch_assoc()) {
                    ?>
                        <option value="<?php echo htmlspecialchars($row['cid']); ?>" <?php echo (isset($_POST['category']) && $_POST['category'] == $row['cid']) ? 'selected="selected"' : ''; ?>>
                            <?php echo htmlspecialchars($row['category_name']); ?>
                        </option>
                    <?php
                    }
                    ?>
                </select>
            </p>
            <p>
                <label style="padding-top:40px;">Select Image 1:</label>
                <input type="file" name="image[]" class="text-long" />
            </p>
            <p id="addcontainer"></p>
            <p id="add_field">
                <span style="color:#0066CC; font-weight:bold; cursor:pointer;">&raquo; Add more images.....</span>
            </p>
            <input type="submit" name="submit" value="Add Image" />
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
