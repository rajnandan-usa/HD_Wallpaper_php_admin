<?php
require_once("thumbnail_images.class.php");
require_once("includes/connection.php");

class k_wallpaper
{
    // Category Query    
    function addCategory()
    {
        global $cn; // Make sure $cn (database connection) is accessible

        $albumimgnm = rand(0, 99999) . "_" . $_FILES['image']['name'];
        $pic1 = $_FILES['image']['tmp_name'];

        if (!is_dir('categories/' . $_POST['category_name'])) {
            mkdir('categories/' . $_POST['category_name'], 0777);
        }

        $tpath1 = 'images/' . $albumimgnm;
        copy($pic1, $tpath1);

        $thumbpath = 'images/thumbs/' . $albumimgnm;
        $obj_img = new thumbnail_images();
        $obj_img->PathImgOld = $tpath1;
        $obj_img->PathImgNew = $thumbpath;
        $obj_img->NewWidth = 72;
        $obj_img->NewHeight = 72;

        if (!$obj_img->create_thumbnail_images()) {
            $_SESSION['msg'] = "Thumbnail not created... please upload image again";
            exit;
        } else {
            $qry = "INSERT INTO `tbl_category` (`category_name`, `category_image`) VALUES (?, ?)";
            $stmt = $cn->prepare($qry);
            $stmt->bind_param("ss", $_POST['category_name'], $albumimgnm);
            $stmt->execute();
        }
    }

    function editCategory()
    {
        global $cn;

        if (empty($_FILES['image']['name'])) {
            if (!is_dir('categories/' . $_POST['category_name'])) {
                mkdir('categories/' . $_POST['category_name'], 0777);
            }

            $qry = "UPDATE `tbl_category` SET `category_name` = ? WHERE cid = ?";
            $stmt = $cn->prepare($qry);
            $stmt->bind_param("si", $_POST['category_name'], $_GET['cat_id']);
            $stmt->execute();
        } else {
            if (!is_dir('categories/' . $_POST['category_name'])) {
                mkdir('categories/' . $_POST['category_name'], 0777);
            }

            $qry = "SELECT `category_image`, `category_name` FROM tbl_category WHERE cid = ?";
            $stmt = $cn->prepare($qry);
            $stmt->bind_param("i", $_GET['cat_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $img_row = $result->fetch_assoc();

            if ($img_row['category_image'] != "") {
                unlink('images/' . $img_row['category_image']);
                unlink('images/thumbs/' . $img_row['category_image']);
            }

            $albumimgnm = rand(0, 99999) . "_" . $_FILES['image']['name'];
            $pic1 = $_FILES['image']['tmp_name'];
            $tpath1 = 'images/' . $albumimgnm;
            copy($pic1, $tpath1);

            $thumbpath = 'images/thumbs/' . $albumimgnm;
            $obj_img = new thumbnail_images();
            $obj_img->PathImgOld = $tpath1;
            $obj_img->PathImgNew = $thumbpath;
            $obj_img->NewWidth = 72;
            $obj_img->NewHeight = 72;

            if (!$obj_img->create_thumbnail_images()) {
                $_SESSION['msg'] = "Thumbnail not created... please upload image again";
                exit;
            } else {
                $qry = "UPDATE `tbl_category` SET `category_name` = ?, `category_image` = ? WHERE cid = ?";
                $stmt = $cn->prepare($qry);
                $stmt->bind_param("ssi", $_POST['category_name'], $albumimgnm, $_GET['cat_id']);
                $stmt->execute();
            }
        }
    }

    function deleteCategory()
    {
        global $cn;

        $qry = "SELECT `category_name`, `category_image` FROM tbl_category WHERE cid = ?";
        $stmt = $cn->prepare($qry);
        $stmt->bind_param("i", $_GET['cat_id']);
        $stmt->execute();
        $cat_img_row = $stmt->get_result()->fetch_assoc();

        $qry = "SELECT `image` FROM tbl_gallery WHERE cat_id = ?";
        $stmt = $cn->prepare($qry);
        $stmt->bind_param("i", $_GET['cat_id']);
        $stmt->execute();
        $img_res = $stmt->get_result();

        while ($img_row = $img_res->fetch_assoc()) {
            unlink('categories/' . $cat_img_row['category_name'] . '/' . $img_row['image']);
        }

        if ($cat_img_row['category_image'] != "") {
            unlink('images/thumbs/' . $cat_img_row['category_image']);
            unlink('images/' . $cat_img_row['category_image']);
        }

        if (is_dir('categories/' . $cat_img_row['category_name'])) {
            rmdir('categories/' . $cat_img_row['category_name']);
        }

        $qry = "DELETE FROM `tbl_category` WHERE cid = ?";
        $stmt = $cn->prepare($qry);
        $stmt->bind_param("i", $_GET['cat_id']);
        $stmt->execute();
    }

    // Image Gallery
    function addImage()
    {
        global $cn;

        $count = count($_FILES['image']['name']);
        for ($i = 0; $i < $count; $i++) {
            $albumimgnm = rand(0, 99999) . "_" . $_FILES['image']['name'][$i];
            $pic1 = $_FILES['image']['tmp_name'][$i];

            $qry = "SELECT `category_name` FROM tbl_category WHERE cid = ?";
            $stmt = $cn->prepare($qry);
            $stmt->bind_param("i", $_POST['category_id']);
            $stmt->execute();
            $cat_img_row = $stmt->get_result()->fetch_assoc();

            $tpath1 = 'categories/' . $cat_img_row['category_name'] . '/' . $albumimgnm;
            copy($pic1, $tpath1);

            $date = date('Y-m-d');
            $qry = "INSERT INTO `tbl_gallery` (`cat_id`, `image_date`, `image`) VALUES (?, ?, ?)";
            $stmt = $cn->prepare($qry);
            $stmt->bind_param("iss", $_POST['category_id'], $date, $albumimgnm);
            $stmt->execute();
        }
    }

    function editImage()
    {
        global $cn;

        $date = date('Y-m-d');

        if (empty($_FILES['image']['name'])) {
            $qry = "UPDATE `tbl_gallery` SET `cat_id` = ?, `image_date` = ? WHERE id = ?";
            $stmt = $cn->prepare($qry);
            $stmt->bind_param("isi", $_POST['category'], $date, $_GET['img_id']);
            $stmt->execute();
        } else {
            $qry = "SELECT `image`, `cat_id` FROM tbl_gallery WHERE id = ?";
            $stmt = $cn->prepare($qry);
            $stmt->bind_param("i", $_GET['img_id']);
            $stmt->execute();
            $img_row = $stmt->get_result()->fetch_assoc();

            $qry = "SELECT `category_name` FROM tbl_category WHERE cid = ?";
            $stmt = $cn->prepare($qry);
            $stmt->bind_param("i", $img_row['cat_id']);
            $stmt->execute();
            $cat_img_row = $stmt->get_result()->fetch_assoc();

            if ($img_row['image'] != "") {
                unlink('categories/' . $cat_img_row['category_name'] . '/' . $img_row['image']);
            }

            $albumimgnm = rand(0, 99999) . "_" . $_FILES['image']['name'];
            $pic1 = $_FILES['image']['tmp_name'];
            $tpath1 = 'categories/' . $cat_img_row['category_name'] . '/' . $albumimgnm;
            copy($pic1, $tpath1);

            $qry = "UPDATE `tbl_gallery` SET `cat_id` = ?, `image_date` = ?, `image` = ? WHERE id = ?";
            $stmt = $cn->prepare($qry);
            $stmt->bind_param("issi", $_POST['category'], $date, $albumimgnm, $_GET['img_id']);
            $stmt->execute();
        }
    }

    function deleteImage()
    {
        global $cn;

        $qry = "SELECT `image`, `cat_id` FROM tbl_gallery WHERE id = ?";
        $stmt = $cn->prepare($qry);
        $stmt->bind_param("i", $_GET['img_id']);
        $stmt->execute();
        $img_row = $stmt->get_result()->fetch_assoc();

        if ($img_row['image'] != "") {
            $qry = "SELECT `category_name` FROM tbl_category WHERE cid = ?";
            $stmt = $cn->prepare($qry);
            $stmt->bind_param("i", $img_row['cat_id']);
            $stmt->execute();
            $cat_img_row = $stmt->get_result()->fetch_assoc();

            unlink('categories/' . $cat_img_row['category_name'] . '/' . $img_row['image']);
        }

        $qry = "DELETE FROM `tbl_gallery` WHERE id = ?";
        $stmt = $cn->prepare($qry);
        $stmt->bind_param("i", $_GET['img_id']);
        $stmt->execute();
    }

    function editProfile()
    {
        global $cn;

        $qry = "UPDATE `tbl_admin` SET `username` = ?, `password` = ?, `email` = ? WHERE id = ?";
        $stmt = $cn->prepare($qry);
        $stmt->bind_param("sssi", $_POST['username'], $_POST['password'], $_POST['email'], $_SESSION['id']);
        $stmt->execute();
    }
}
?>
