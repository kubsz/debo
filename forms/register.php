<?php
include($_SERVER['DOCUMENT_ROOT']."/include/dbconnect.php");

$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];
$first_name = $_POST['first-name'];
$last_name = $_POST['last-name'];

$error = false;
$errormessage = "";

if(!(preg_match('/^[a-zA-Z0-9]+$/', $username)) && $username > 2 && $username <= 16 ) {
    $error = true;
    $errormessage = "Username is not valid";
}

if(strlen($password) < 6) {
    $error = true;
    $errormessage = "Password is too short";
}
if(strlen($password) > 64) {
    $error = true;
    $errormessage = "Password is too long";
}

if(strlen($first_name) < 2) {
    $error = true;
    $errormessage = "First Name is too short";
}

if(strlen($first_name) > 32) {
    $error = true;
    $errormessage = "First Name is too long";
}

if(strlen($last_name) < 2) {
    $error = true;
    $errormessage = "Last Name is too short";
}

if(strlen($last_name) > 32) {
    $error = true;
    $errormessage = "Last Name is too long";
}

$checkusername = "SELECT * FROM users where username = '$username';";
$checkusernameresult = mysqli_query($conn, $checkusername);

$checkemail = "SELECT * FROM users where email_address = '$email';";
$checkemailresult = mysqli_query($conn, $checkemail);


if($checkemailresult && mysqli_num_rows($checkemailresult) > 0){
    $error = true;
    $errormessage = "Email address has already been taken.";
}
if($checkusernameresult && mysqli_num_rows($checkusernameresult) > 0){
    $error = true;
    $errormessage = "Username has already been taken.";
}

$phash = password_hash($password, PASSWORD_DEFAULT);
$ip = $_SERVER['REMOTE_ADDR'];
$date = time();
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

if($error == false) {
    $sql = "INSERT INTO `users`(`username`, `password`, `email_address`, `date_created`, `first_name`, `last_name`, `last_online`, `login_session`, `ip`)VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $ps = $conn->prepare($sql);
    $ps->bind_param("sssississ", $username, $phash, $email, $date, $first_name, $last_name, $date, $login_session, $ip);
    $ps->execute();
    $result = $ps->get_result();

    $sql = "INSERT INTO `user_images`(`user_id`)VALUES(?)";

    $ps = $conn->prepare($sql);
    $ps->bind_param("sssississ", $username, $phash, $email, $date, $first_name, $last_name, $date, $login_session, $ip);
    $ps->execute();
    $result = $ps->get_result();

    $cookie_name = 'logsession';
    $cookie_value = $login_session;
    setcookie($cookie_name, $cookie_value, time() + (14400), "/");

    echo "true";
}
else
    echo $errormessage;

?>
