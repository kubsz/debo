<?php

include($_SERVER['DOCUMENT_ROOT']."/include/checkuser.php");
if($session_user_id == null) {
    echo "n";
    die();
}

$post_id = $_POST['post_id'];
$interaction = $_POST['interaction'];

$date = time();

$sql = "SELECT * FROM interactions WHERE user_id=? AND post_id=? AND interaction=?";

$ps = $conn->prepare($sql);
$ps->bind_param("iii", $session_user_id, $post_id, $interaction);
$ps->execute();
$result = $ps->get_result();

if ($result->num_rows > 0) {

    $sql = "DELETE FROM interactions WHERE user_id=$session_user_id AND post_id=$post_id AND interaction=$interaction";

    $result = $conn->query($sql);
    $result->execute;

    echo $interaction."f";
    die();
}
else {
    $sql = "INSERT INTO `interactions`(`user_id`, `post_id`, `interaction`, `date_of_interaction`)VALUES(?, ?, ?, ?)";

    $ps = $conn->prepare($sql);
    $ps->bind_param("iiii", $session_user_id, $post_id, $interaction, $date);
    $ps->execute();
    $result = $ps->get_result();

    echo $interaction."w";
}

?>