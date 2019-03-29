<?php
include($_SERVER['DOCUMENT_ROOT']."/include/checkuser.php");

$user_id = $_POST['user_id'];
$post_comment_id = $_POST['post_comment_id'];

if($session_user_id != $user_id) {
    echo "error";
    die();
}

if($session_user_id == 1) {
    $sql = "DELETE FROM post_comments WHERE post_comment_id=?";

    $ps = $conn->prepare($sql);
    $ps->bind_param("i", $post_comment_id);
    $ps->execute();
    $result = $ps->get_result();
}
else {
    $sql = "DELETE FROM post_comments WHERE post_comment_id=? AND post_commenter_id=?";

    $ps = $conn->prepare($sql);
    $ps->bind_param("ii", $post_comment_id, $user_id);
    $ps->execute();
    $result = $ps->get_result();
}
echo "true";
?>