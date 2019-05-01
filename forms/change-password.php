<?php

include($_SERVER['DOCUMENT_ROOT']."/include/checkuser.php");

$current_password = $_POST['current_password'];
$new_password = $_POST['new_password'];
$confirm_password = $_POST['confirm_password'];

if($new_password != $confirm_password) {
    echo "Passwords are not the same";
    die();
}

$phash = password_hash($confirm_password, PASSWORD_DEFAULT);

$sql = "SELECT password FROM users WHERE user_id=?";

$ps = $conn->prepare($sql);
$ps->bind_param("i", $session_user_id);
$ps->execute();
$result = $ps->get_result();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $password = $row["password"];
    }
} else {
    echo "invalid password";
    die();
}

if (password_verify($current_password, $password )){
    $sql = "UPDATE users SET password=? WHERE user_id=?";

    $ps = $conn->prepare($sql);
    $ps->bind_param("si", $phash, $session_user_id);
    $ps->execute();
    $result = $ps->get_result();

    echo "Password changed!";
} else
    echo "Cannot change password";

?>