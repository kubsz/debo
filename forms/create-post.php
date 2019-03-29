<?php

$content =  $_POST["content"];
if(strlen($content) > 256 || strlen($content) == 0) {
    header("Location: /");
    die();
}

$date = time();
include($_SERVER['DOCUMENT_ROOT']."/include/checkuser.php");

if($session_user_id == null) {
    header("Location: /");
    die();
}

$sql = "INSERT INTO `posts`(`author_id`, `content`, `date_posted`)VALUES(?, ?, ?)";

$ps = $conn->prepare($sql);
$ps->bind_param("isi", $session_user_id, $content, $date);
$ps->execute();
$result = $ps->get_result();

header("Location: /");
die();

?>