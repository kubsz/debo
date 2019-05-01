<?php

include($_SERVER['DOCUMENT_ROOT']."/include/checkuser.php");

$new_bio = $_POST['bio'];

$sql = "UPDATE users SET bio=? WHERE user_id=?";

$ps = $conn->prepare($sql);
$ps->bind_param("si", $new_bio, $session_user_id);
$ps->execute();
$result = $ps->get_result();

echo "true";

?>