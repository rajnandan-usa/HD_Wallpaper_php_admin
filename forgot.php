<?php
include("includes/connection.php");

// Validate email
if (empty($_POST["email"])) {
    echo "<script>document.location='index.php?msg=9';</script>";
    exit;
} elseif (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
    echo "<script>document.location='index.php?msg=10';</script>";
    exit;
} else {
    // Connect to the database
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $email = $conn->real_escape_string($_POST["email"]);

    $qry = "SELECT * FROM tbl_admin WHERE email=?";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        $to = $row['email'];
        $subject = 'HD-Wallpaper Admin Password';
        
        $message = '
            <div>
               <strong>Username</strong>: ' . htmlspecialchars($row['username']) . '<br>
               <strong>Password</strong>: ' . htmlspecialchars($row['password']) . '<br>
            </div>
        ';

        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        
        // Send the email
        if (mail($to, $subject, $message, $headers)) {
            echo "<script>document.location='index.php?msg=11';</script>";
        } else {
            echo "<script>document.location='index.php?msg=12';</script>";
        }
    } else {
        echo "<script>document.location='index.php?msg=8';</script>";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
