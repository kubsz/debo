<?php

include($_SERVER['DOCUMENT_ROOT']."/include/checkuser.php");

$user2_id = $_POST['user_id'];
$date = time();

if($session_user_id == $user2_id) {
    echo "error";
    die();
}

$sql = "SELECT * FROM following WHERE user1_id=? AND user2_id=?";

$ps = $conn->prepare($sql);
$ps->bind_param("ii", $session_user_id, $user2_id);
$ps->execute();
$result = $ps->get_result();

if ($result->num_rows > 0) {

    $sql = "DELETE FROM following WHERE user1_id=$session_user_id AND user2_id=$user2_id";

    $result = $conn->query($sql);
    $result->execute;

    echo "unfollow";
    die();
}
else {
    $sql = "INSERT INTO `following`(`user1_id`, `user2_id`, `date_followed`)VALUES(?, ?, ?)";

    $ps = $conn->prepare($sql);
    $ps->bind_param("iii", $session_user_id, $user2_id, $date);
    $ps->execute();
    $result = $ps->get_result();
}

?>