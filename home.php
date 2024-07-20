<?php 
include("includes/header.php");
// include("includes/connection.php"); // Ensure the database connection is included
?>

<!-- h2 stays for breadcrumbs -->
<h2><a href="#">Dashboard</a> &raquo; <a href="#" class="active">Print resources</a></h2>

<div id="main">

    <h3 align="left">Total Categories</h3>
    <?php 
        $qry_cat = "SELECT COUNT(*) as num FROM tbl_category";
        if ($result = $cn->query($qry_cat)) {
            $total_category = $result->fetch_assoc();
            $total_category = $total_category['num'];
        } else {
            $total_category = 0;
        }
    ?>  
    <p style="margin-left:50px;">
        <a href="manage_category.php" style="color:#009900;text-decoration:none; font-size:16px;">
            <?php echo $total_category; ?>
        </a>
    </p>

    <h3 align="right" style="margin-top:-40px">Total Images</h3>
    <?php 
        $qry_gallery = "SELECT COUNT(*) as num FROM tbl_gallery";
        if ($result = $cn->query($qry_gallery)) {
            $total_images = $result->fetch_assoc();
            $total_images = $total_images['num'];
        } else {
            $total_images = 0;
        }
    ?>  
    <p align="right" style="margin-right:45px; margin-bottom:50px;">
        <a href="manage_gallery.php" style="color:#009900;text-decoration:none; font-size:16px;">
            <?php echo $total_images; ?>
        </a>
    </p>
</div>
<!-- // #main -->

<div class="clear"></div>
</div>
<!-- // #container -->
</div>    
<!-- // #containerHolder -->

<?php include("includes/footer.php"); ?>
