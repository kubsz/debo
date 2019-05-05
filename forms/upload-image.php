<?php
include($_SERVER['DOCUMENT_ROOT']."/include/checkuser.php");

$target_dir = "../img/user-images/";
$target_file = $target_dir . basename($_FILES["image"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
$imageName = $_FILES["image"]["name"];

if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if($check !== false) {
        $uploadOk = 1;
    } else {
        $uploadOk = 0;
    }
}

if (file_exists($target_file)) {
    $target_file = substr($target_file, 0, -4).$user_id.".".$imageFileType;
    $imageName = substr($imageName, 0, -4).$user_id.".".$imageFileType;
}

if ($_FILES["image"]["size"] > 2000000) {
    echo "Sorry, your file is too large.";
    $uploadOk = 0;
}

if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "gif" ) {
    $uploadOk = 0;
}
if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";

} else {
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        echo "The file ". basename( $_FILES["image"]["name"]). " has been uploaded.";
    } else {
        echo "Sorry, there was an error uploading your file.";

    }
}

if($uploadOk == 1) {
    $date = time();
    $sql = "SELECT * FROM user_images WHERE user_id='$session_user_id'";
    $result = mysqli_query($conn, $sql);

    if($result->num_rows > 0) {
        $sql = "DELETE FROM user_images WHERE user_id=$session_user_id";
        $result = $conn->query($sql);
    }
    $sql = "INSERT INTO user_images (user_id, image_name, image_upload_date) VALUES(?, ?, ?)";

    $ps = $conn->prepare($sql);
    $ps->bind_param("isi", $session_user_id, $imageName, $date);
    $ps->execute();
    $result = $ps->get_result();
}



?>