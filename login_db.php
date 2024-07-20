<?php
include("includes/connection.php");
session_start();

$username = trim($_POST['username']);
$password = trim($_POST['password']);

if ($username == "") {
    echo "<script>document.location='index.php?msg=1';</script>";
} else if ($password == "") {
    echo "<script>document.location='index.php?msg=2';</script>";
} else {
    // Using prepared statements to prevent SQL injection
    $qry = "SELECT * FROM tbl_admin WHERE username = ? AND password = ?";
    $stmt = $cn->prepare($qry);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        $_SESSION['id'] = $row['id'];
        $_SESSION['admin_name'] = $row['username'];

        if (isset($_POST['remember'])) {
            setcookie("id", $_SESSION['id'], time() + 60 * 60 * 24, "/");
            setcookie("admin_name", $_SESSION['admin_name'], time() + 60 * 60 * 24, "/");
        }

        echo "<script>document.location='home.php';</script>";
    } else {
        echo "<script>document.location='index.php?msg=4';</script>";
    }

    $stmt->close();
}

$cn->close();
?>
