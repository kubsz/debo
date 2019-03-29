<!DOCTYPE HTML>
<html lang="en">
<head>
    <?php include($_SERVER["DOCUMENT_ROOT"]."/include/head.php"); ?>
    <link rel="stylesheet" type="text/css" href="/css/login.css">
</head>
<body>
    <?php include($_SERVER['DOCUMENT_ROOT']."/include/nav.php"); ?>
    <div class="section blue">
    	<div class="margined">
    		<div class="left container">
                <h2>Register</h2>
                <form id="register-form" method="post" action="/forms/register.php">
                    <label id="username-label">Username</label>
                    <input autocomplete="off" name="username" id="username">
                    <label id="email-label">Email Address</label>
                    <input autocomplete="off" name="email" id="email">
                    <label id="password-label">Password</label>
                    <input autocomplete="off" name="password" type="password" id="password">
                    <label id="password2-label">Confirm Password</label>
                    <input autocomplete="off" name="password2" type="password" id="password2">
                    <p id="error-text"></p>
                    <button id="reg-button" class="reg-button" type="submit">Register</button>
                </form>
            </div>
            <div class="right container">
                <h2>Login</h2>
                <form id="login-form" method="post" action="/forms/login.php">
                    <label id="lusername-label">Username</label>
                    <input autocomplete="off" name="username" id="lusername">
                    <label id="lpassword-label">Password</label>
                    <input autocomplete="off" name="password" type="password" id="lpassword">
                    <p id="error-text-log"></p>
                    <button id="log-button" class="reg-button" type="submit">Login</button>
                </form>
            </div>
    	</div>
    </div>
</body>
<script src="/script/reg.js"></script>
<script src="/script/log.js"></script>
<script src="/script/nav.js"></script>
<script>
    $("#login-option").addClass('active-nav');
</script>
</html>