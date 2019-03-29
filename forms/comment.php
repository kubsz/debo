<?php

$comment = $_POST["comment"];
$user_id = $_POST["user_id"];
$post_id = $_POST["post_id"];

if(strlen($comment) > 256 || strlen($comment) == 0) {
    echo "n";
    die();
}
$date = time();
include($_SERVER['DOCUMENT_ROOT']."/include/checkuser.php");

if($session_user_id != $user_id) {
	echo "n";
	die();
}

$sql = "INSERT INTO `post_comments`(`post_id`, `post_commenter_id`, `post_comment_content`, `date_commented`)VALUES(?, ?, ?, ?);";

$ps = $conn->prepare($sql);
$ps->bind_param("iisi", $post_id, $user_id, $comment, $date);
$ps->execute();
$result = $ps->get_result();


$sql = "SELECT post_comment_id FROM post_comments ORDER BY post_comment_id DESC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $post_comment_id = $row["post_comment_id"];
    }
}


echo $session_user_id.",".$post_comment_id.",".$post_id;
?>