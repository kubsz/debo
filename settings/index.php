<?php
include($_SERVER['DOCUMENT_ROOT']."/include/checkuser.php");

if($session_user_id == null) {
    include($_SERVER["DOCUMENT_ROOT"]."/include/404.php");
    die();
}

$sql = "SELECT * FROM users WHERE user_id = ?";

$ps = $conn->prepare($sql);
$ps->bind_param("s", $session_user_id);
$ps->execute();
$result = $ps->get_result();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $user_id = $row["user_id"];
        $username = htmlentities($row["username"]);
        $email = htmlentities($row["email_address"]);
        $firstname = htmlentities($row["first_name"]);
        $last_online = date("Y-m-d H:i:s", substr($row["last_online"], 0, 10));
        if($firstname == null)
            $firstname = "John";
        $lastname = htmlentities($row["last_name"]);
        if($lastname == null)
            $lastname = "Doe";
        $age = htmlentities($row["age"]);
        if($row['bio'] != null)
            $bio = htmlentities($row['bio']);
        else
            $bio = "Default Bio";
        $date_created = $row["date_created"];
    }
} else {
    include($_SERVER["DOCUMENT_ROOT"]."/include/404.php");
    die();
}

function splitTime($time) {
    switch ($time) {
        case $time == 1:
            return "1 second ago";
            break;
        case $time < 60:
            return floor($time)." seconds ago";
            break;
        case $time > 60 && $time < 120:
            return floor($time / 60)." minute ago";
            break;
        case $time > 60 && $time < 3600:
            return floor($time / 60)." minutes ago";
            break;
        case $time > 3600 && $time < 7200:
            return floor($time / 3600)." hour ago";
            break;
        case $time > 3600 && $time < 86400:
            return floor($time / 3600)." hours ago";
            break;
    }
}

?>
<!DOCTYPE HTML>
<html lang="en">
<head>
    <title><?php echo $username ?> - DEBO</title>
    <?php include($_SERVER["DOCUMENT_ROOT"]."/include/head.php"); ?>
    <link rel="stylesheet" type="text/css" href="/css/settings.css">
</head>
<body>
    <div class="modal-background" id="modal-background">
        <div class="modal">
            <div class="splitter"></div>
            <div class="inner-modal">

            </div>
        </div>
    </div>
    <?php include($_SERVER["DOCUMENT_ROOT"]."/include/nav.php"); ?>
    <div class="section">
        <div class="blue-section"></div>
        <div class="margined">
            <h1>Settings</h1>
            <h4>Settings and information to keep your account secure.</h4>
            <div class="box">
                <div class="left-box">
                    <div class="inner-box">
                        <h2>Email Address</h2>
                        <h3><span class="blue-text"><?php echo $email ?></span> - not verified </h3>
                        <p>Your email address has not been verified, <span>Click to send verification email.</span></p>
                    </div>
                </div>
                <div class="right-box button" id="change-email"><h2>Change Email<br>Address</h2></div>
                <div class="clear"></div>
            </div>
            <br>
            <div class="box">
                <div class="left-box">
                    <div class="inner-box">
                        <h2>Username</h2>
                        <h3 style="color:rgba(40,40,40,0.75);">Current username: <span class="blue-text"><?php echo $username ?></span></h3>
                    </div>
                </div>
                <div class="right-box button" id="change-username"><h2>Change<br>Username</h2></div>
                <div class="clear"></div>
            </div>
            <br>
            <div class="box">
                <div class="left-box">
                    <div class="inner-box">
                        <h2>Password</h2>
                        <h3 style="color:rgba(40,40,40,0.75);">Last online: <span class="blue-text"><?php echo $last_online; ?></span></h3>
                    </div>
                </div>
                <div class="right-box button" id="change-password"><h2>Change<br>Password</h2></div>
                <div class="clear"></div>
            </div>
            <br>
            <div class="box">
                <div class="left-box">
                    <div class="inner-box">
                        <h2>Bio</h2>
                        <h3 style="color:rgba(40,40,40,0.75);">Current Bio: <span class="blue-text"><?php echo $bio; ?></span></h3>
                    </div>
                </div>
                <div class="right-box button" id="change-bio"><h2>Change<br>Bio</h2></div>
                <div class="clear"></div>
            </div>
            <br><br>
        </h1>
    </div>
</body>
<script>
    var email = "<?php echo $email; ?>";
    var username = "<?php echo $username; ?>";
    var bio = <?php echo json_encode($bio); ?>;
</script>
<script src="/script/nav.js"></script>
<script src="/script/settings.js"></script>
</html>