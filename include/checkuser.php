<?php

include($_SERVER['DOCUMENT_ROOT']."/include/dbconnect.php");

$login_session = $_COOKIE['logsession'];

$sql = "SELECT * FROM users WHERE login_session = ?";

$ps = $conn->prepare($sql);
$ps->bind_param("s", $login_session);
$ps->execute();
$result = $ps->get_result();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $session_user_id = $row["user_id"];
        $session_user_username = $row["username"];
    }
}

?>