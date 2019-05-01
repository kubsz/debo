<?php
include($_SERVER['DOCUMENT_ROOT']."/include/dbconnect.php");

$username = $_POST["username"];
$password_input = $_POST["password"];
$date = time();

$sql = "SELECT * FROM users WHERE username=?";

$ps = $conn->prepare($sql);
$ps->bind_param("s", $username);
$ps->execute();
$result = $ps->get_result();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $password = $row["password"]; 
    }
}

if (password_verify($password_input , $password )){

    function generateRandomString($length = 64) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $characters_length = strlen($characters);
        $random_string = '';
        for ($i = 0; $i < $length; $i++) {
            $random_string .= $characters[rand(0, $characters_length - 1)];
        }
        return $random_string;
    }
    $login_session = generateRandomString();

    $sql = "UPDATE users SET last_online=?, login_session=? WHERE username=?";

    $ps = $conn->prepare($sql);
    $ps->bind_param("iss", $date, $login_session, $username);
    $ps->execute();
    $result = $ps->get_result();

    $cookie_name = "logsession";
	$cookie_value = $login_session;
	setcookie($cookie_name, $cookie_value, time() + (2419200), "/");
	echo "true";
}
else {
   	echo "error occured, please try again.";
}
?>
