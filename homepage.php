<?php

include($_SERVER['DOCUMENT_ROOT']."/include/dbconnect.php");

$sql = "SELECT user_id from users";
$count_users = 0;
$result = $conn->query($sql);
if($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $count_users++;
    }
}

$sql = "SELECT post_id from posts";
$count_posts = 0;
$result = $conn->query($sql);
if($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $count_posts++;
    }
}
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
    <title>DEBO - Social Media Platform</title>
    <link rel="stylesheet" type="text/css" href="/css/logreg.css">
    <?php include($_SERVER["DOCUMENT_ROOT"]."/include/head.php"); ?>
</head>
<body>
<div id="login-modal-background" class="modal-background">
    <div id="login-modal" class="modal login-modal">
        <h2>Login With DEBO</h2>
        <form id="login-form" action="/forms/login.php" method="post">
            <input type="text" id="log-username" name="username">
            <span id="log-username-label" class="label register-username">Username</span>
            <i id="log-username-icon" class="icon fas fa-times login-username"></i>
            <input type="password" id="log-password" name="password">
            <span id="log-password-label" class="label login-password">Password</span>
            <i id="log-password-icon" class="icon fas fa-times register-password"></i>
            <input class="log-button button-invalid" type="submit" value="Submit">
            <p id="log-error-text"></p>
        </form>
        <div class="modal-footer">
            <p>Don't have an account? <a href="#" onclick="switchModal('login', 'register')">Register</a> now!</p>
        </div>
    </div>
</div>
<div id="register-modal-background" class="modal-background">
    <div id="register-modal" class="modal register-modal">
        <h2>Register With DEBO</h2>
        <form autocomplete="false" id="register-form" action="/forms/register.php" method="post">
            <input type="text" id="username" name="username">
            <span id="username-label" class="label register-username">Username</span>
            <i id="username-icon" class="icon fas fa-times register-username"></i>
            <input type="text" id="email" name="email">
            <span id="email-label" class="label register-email">Email</span>
            <i id="email-icon" class="icon fas fa-times register-email"></i>
            <input type="password" id="password" name="password">
            <span id="password-label" class="label register-password">Password</span>
            <i id="password-icon" class="icon fas fa-times register-password"></i>
            <input class="splitter" type="text" id="first-name" name="first-name">
            <span id="first-name-label" class="label register-first-name">First Name</span>
            <i id="first-name-icon" class="icon fas fa-times register-first-name"></i>
            <input type="text" id="last-name" name="last-name">
            <span id="last-name-label" class="label register-last-name">Last Name</span>
            <i id="last-name-icon" class="icon fas fa-times register-last-name"></i>
            <input class="reg-button button-invalid" type="submit" value="Submit">
            <p id="reg-error-text"></p>
        </form>
        <div class="modal-footer">
            <p>Already have an account? <a href="#" onclick="switchModal('register', 'login')">Log In</a> now!</p>
        </div>
    </div>
</div>

<?php
if(isset($_COOKIE['logsession'])) include($_SERVER["DOCUMENT_ROOT"]."/include/nav.php");
else include($_SERVER["DOCUMENT_ROOT"]."/include/nav-nl.php");
?>
<div class="section sec1">
    <img src="https://www.campaignmonitor.com/assets/uploads/2010/12/background_d.jpg">
    <div class="margined">
        <div class="left-header">
            <h1>INNOVATIVE SOCIAL<br>MEDIA PLATFORM</h1>
            <p>Experience and share your greatest memories</p>
            <div class="button register">Register</div>
            <div class="button login">Login</div>
        </div>
    </div>
</div>
<div class="section sec2">
    <div class="margined">
        <div class="banner">
            <div class="live-stats">
                <h2><?php echo $count_users; ?></h2>
                <p>Users</p>
            </div>
            <div class="live-stats">
                <h2><?php echo $count_posts; ?></h2>
                <p>Posts</p>
            </div>
            <div class="live-stats" style="border:0">
                <h2>0</h2>
                <p>Messages</p>
            </div>
        </div>
        <h2>An innovative platform with security and anonimity at its core. Utilize a selection of unique features never seen before.</h2>
        <div class="img-container">
            <div class="phone">
                <div class="screen">
                    <img id="phone-screen" src="https://i.imgur.com/9Tw5RRI.png">
                </div>
                <div class="topline"></div>
                <div class="camera"></div>
                <div class="but"></div>
            </div>
            <img id="shield" src="https://images.vexels.com/media/users/3/142812/isolated/preview/992801ae3182fa95353e941cfcac9293-shield-logo-emblem-design-by-vexels.png">
        </div>
        <div class="row-container">
            <div class="row" id="row-1">
                <div class="ico">
                    <div class="circle blue">
                        <i class="fas fa-tablet-alt"></i>
                        <div class="highlight"></div>
                    </div>
                </div>
                <div class="text">
                    <h3 class="blue-color">RESPONSIVE</h3>
                    <p>Mobile responsive, user-friendly front-end which is appealing on all devices.</p>
                </div>
            </div>
            <div class="row" id="row-2">
                <div class="ico">
                    <div class="circle purple">
                        <i class="fas fa-shield-alt"></i>
                        <div class="highlight"></div>
                    </div>
                </div>
                <div class="text">
                    <h3 class="purple-color">SECURITY</h3>
                    <p>idk something about security and how debo is safe cos it's not run by mr zuckerberg</p>
                </div>
            </div>
            <div class="row" id="row-3">
                <div class="ico">
                    <div class="circle orange">
                        <i class="fas fa-handshake"></i>
                        <div class="highlight"></div>
                    </div>
                </div>
                <div class="text">
                    <h3 class="orange-color">CHAT WORLDWIDE</h3>
                    <p>A secure chat application using the DEBO platform.</p>
                </div>
            </div>
        </div>
        <div class="clear"></div>
        <br>
        <br>
    </div>
</div>
</body>
<script src="/script/nav.js"></script>
<script src="/script/home-nl.js"></script>
<script>
    $("#home-option").addClass('active-nav');
</script>
</html>