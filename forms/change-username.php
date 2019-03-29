<?php

include($_SERVER['DOCUMENT_ROOT']."/include/checkuser.php");

$new_username = $_POST['username'];

if(!(preg_match('/^[a-zA-Z0-9]{3,}$/', $new_username))) {
    echo "Username is not valid";
    die();
}

$sql = "SELECT * FROM users WHERE username=?";

$ps = $conn->prepare($sql);
$ps->bind_param("s", $new_username);
$ps->execute();
$result = $ps->get_result();

if ($result->num_rows > 0) {
    echo "Username already taken";
    die();
}

$sql = "UPDATE users SET username=? WHERE user_id=?";

$ps = $conn->prepare($sql);
$ps->bind_param("si", $new_username, $session_user_id);
$ps->execute();
$result = $ps->get_result();

echo "true";

?>