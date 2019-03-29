<?php
include($_SERVER['DOCUMENT_ROOT']."/include/checkuser.php");

$user_id = $_POST['user_id'];
$post_id = $_POST['post_id'];

if($session_user_id != $user_id) {
    echo "error";
    die();
}

$sql = "SELECT * FROM posts WHERE post_id=? AND author_id=?";

$ps = $conn->prepare($sql);
$ps->bind_param("ii", $post_id, $user_id);
$ps->execute();
$result = $ps->get_result();

if($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $author_id = $row['author_id'];
    }
}

if($session_user_id == 1) { //if user is an admin (can delete all posts)
    $sql = "DELETE FROM posts WHERE post_id=?";

    $ps = $conn->prepare($sql);
    $ps->bind_param("i", $post_id);
    $ps->execute();
    $result = $ps->get_result();

    $sql = "DELETE FROM post_comments WHERE post_id=?";

    $ps = $conn->prepare($sql);
    $ps->bind_param("i", $post_id);
    $ps->execute();
    $result = $ps->get_result();

    $sql = "DELETE FROM interactions WHERE post_id=?";

    $ps = $conn->prepare($sql);
    $ps->bind_param("i", $post_id);
    $ps->execute();
    $result = $ps->get_result();

    echo "true";
}
else if($author_id == $user_id) {
    $sql = "DELETE FROM posts WHERE post_id=? AND author_id=?";

    $ps = $conn->prepare($sql);
    $ps->bind_param("ii", $post_id, $user_id);
    $ps->execute();
    $result = $ps->get_result();

    $sql = "DELETE FROM post_comments WHERE post_id=?";

    $ps = $conn->prepare($sql);
    $ps->bind_param("i", $post_id);
    $ps->execute();
    $result = $ps->get_result();

    $sql = "DELETE FROM interactions WHERE post_id=?";

    $ps = $conn->prepare($sql);
    $ps->bind_param("i", $post_id);
    $ps->execute();
    $result = $ps->get_result();

    echo "true";
}
else
    echo "fail";
?>