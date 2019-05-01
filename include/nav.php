<div class="nav">
    <div class="margined">
        <div class="logo">
            <a href="/">DEBO</a>
        </div>
        <div class="options">
            <?php
            if(isset($_COOKIE['logsession'])) {

                include($_SERVER['DOCUMENT_ROOT']."/include/dbconnect.php");

                $login_session = $_COOKIE['logsession'];

                $sql = "SELECT * FROM users WHERE login_session = ?";

                $ps = $conn->prepare($sql);
                $ps->bind_param("s", $login_session);
                $ps->execute();
                $result = $ps->get_result();

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $session_username = htmlentities($row["username"]);
                    }
                } else {
                    setcookie('logsession', null, time() - (14400), '/');
                    header("Location: /");
                }

                echo "<div class='dropdown'>
                        <p>$session_username<i class='fas fa-caret-down'></i></p>
                        <div class='lower-dropdown'>
                            <a style='border-top-width:0;' href='/user/".strtolower($session_username)."'>Profile</a>
                            <a href='/settings/'>Settings</a>    
                            <a href='/forms/logout.php'>Logout</a>
                        </div>
                    </div>
                    <a id='home-option' href='/'>Home</a>";
            }
            else echo "<a id=\"home-option\" href=\"/\">Home</a>";
            ?>
            <a id='faq-option' href="/frequently-asked-questions/">Frequently Asked Questions</a>
        </div>
    </div>
</div>