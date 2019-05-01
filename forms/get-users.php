<?php
include($_SERVER['DOCUMENT_ROOT']."/include/checkuser.php");

$search = $_POST['search']."%";
if(strlen($search) < 2) {
    echo "";
    die();
}

$sql = "SELECT username FROM users WHERE username LIKE ?";

$ps = $conn->prepare($sql);
$ps->bind_param("s", $search);
$ps->execute();
$result = $ps->get_result();

$following_count = 0;
if($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $string .= "<a href='/user/".$row['username']."'>".$row['username']."</a>";
    }
}
echo $string;
?>