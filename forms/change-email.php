<?php

include($_SERVER['DOCUMENT_ROOT']."/include/checkuser.php");

$new_email = $_POST['email'];

if(!(preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,7})$/', $new_email))) {
    echo "Email Address is not valid";
    die();
}

$sql = "SELECT * FROM users WHERE email_address=?";

$ps = $conn->prepare($sql);
$ps->bind_param("s", $new_email);
$ps->execute();
$result = $ps->get_result();

if ($result->num_rows > 0) {
    echo "Email Address already taken";
    die();
}

$sql = "UPDATE users SET email_address=? WHERE user_id=?";

$ps = $conn->prepare($sql);
$ps->bind_param("si", $new_email, $session_user_id);
$ps->execute();
$result = $ps->get_result();

echo "true";

?>