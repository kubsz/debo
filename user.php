<?php
include($_SERVER['DOCUMENT_ROOT']."/include/checkuser.php");


$get_username = $_GET['user'];

$sql = "SELECT * FROM users WHERE username = ?";

$ps = $conn->prepare($sql);
$ps->bind_param("s", $get_username);
$ps->execute();
$result = $ps->get_result();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $user_id = $row["user_id"];
        $username = htmlentities($row["username"]);
        $email = htmlentities($row["email_address"]);
        $firstname = htmlentities($row["first_name"]);
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
}
else {
    include($_SERVER["DOCUMENT_ROOT"]."/include/404.php");
    die();
}

if($session_user_id == $user_id) {
    $same_user = true;
} else {
    $same_user = false;
}

$total_likes_sql = "SELECT COUNT(*) FROM interactions WHERE user_id=$user_id AND interaction=1";

$result = $conn->query($total_likes_sql);
$row = mysqli_fetch_row($result);
$total_likes = $row[0];

$total_shares_sql = "SELECT COUNT(*) FROM interactions WHERE user_id=$user_id AND interaction=2";

$result = $conn->query($total_shares_sql);
$row = mysqli_fetch_row($result);
$total_shares = $row[0];

$total_posts_sql = "SELECT COUNT(*) FROM posts WHERE author_id=$user_id";

$result = $conn->query($total_posts_sql);
$row = mysqli_fetch_row($result);
$total_posts = $row[0];

$following_sql = "SELECT COUNT(*) FROM following WHERE user1_id=$user_id";

$result = $conn->query($following_sql);
$row = mysqli_fetch_row($result);
$following = $row[0];

$followers_sql = "SELECT COUNT(*) FROM following WHERE user2_id=$user_id";

$result = $conn->query($followers_sql);
$row = mysqli_fetch_row($result);
$followers = $row[0];

$date =  gmdate("d-m-Y", $date_created);

$user_image_sql = "SELECT image_name FROM user_images WHERE user_image_id=$user_id";

$result = $conn->query($user_image_sql);
if($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        if($row['image_name'] != null)
            $user_image = htmlentities($row['image_name']);
        else
            $user_image = "user.png";
    }
}

$posts_sql = "SELECT * FROM posts WHERE author_id=$user_id ORDER BY date_posted DESC";

$result = $conn->query($posts_sql);
$post_count = 0;

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $post_id[$post_count] = $row['post_id'];
        $post_content[$post_count] = htmlentities($row['content']);
        if((time() - $row['date_posted']) >  86400)
            $post_date_posted[$post_count] =  gmdate("d-m-Y", $row['date_posted']);
        else
            $post_date_posted[$post_count] = splitTime(time() - $row['date_posted']);

        $likes_sql = "SELECT COUNT(*) FROM interactions WHERE post_id=".$row['post_id']." AND interaction=1";
        $likes_result = $conn->query($likes_sql);
        $likes_row = mysqli_fetch_row($likes_result);
        $post_likes[$post_count] = $likes_row[0];

        $shares_sql = "SELECT COUNT(*) FROM interactions WHERE post_id=".$row['post_id']." AND interaction=2";
        $shares_result = $conn->query($shares_sql);
        $shares_row = mysqli_fetch_row($shares_result);
        $post_shares[$post_count] = $shares_row[0];

        $user_likes_post_sql = "SELECT * FROM interactions WHERE user_id=$session_user_id AND post_id=".$row['post_id']." AND interaction=1";
        $user_likes_post_result = $conn->query($user_likes_post_sql);
        if($user_likes_post_result->num_rows > 0)
            $user_likes_post[$post_count] = true;
        else
            $user_likes_post[$post_count] = false;

        $user_shares_post_sql = "SELECT * FROM interactions WHERE user_id=$session_user_id AND post_id=".$row['post_id']." AND interaction=2";
        $user_shares_post_result = $conn->query($user_shares_post_sql);
        if($user_shares_post_result->num_rows > 0)
            $user_shares_post[$post_count] = true;
        else
            $user_shares_post[$post_count] = false;

        $comments_sql = "SELECT * FROM post_comments INNER JOIN users ON post_comments.post_commenter_id = users.user_id WHERE post_id=$post_id[$post_count]";

        $comments_sql = "SELECT * FROM ((post_comments
        INNER JOIN users ON post_comments.post_commenter_id = users.user_id)
        INNER JOIN user_images ON users.user_id = user_images.user_image_id)
        WHERE post_id=$post_id[$post_count]";


        $comments_result = $conn->query($comments_sql);
        $count_comments[$post_count] = 0;

        if($comments_result->num_rows > 0) {
            while($row = $comments_result->fetch_assoc()) {
                $post_comment_id[$post_count][$count_comments[$post_count]] = $row['post_comment_id'];
                $comment_post_id[$post_count][$count_comments[$post_count]] = $row['post_id'];
                $post_commenter_id[$post_count][$count_comments[$post_count]] = $row['post_commenter_id'];
                if($row['image_name'] != null)
                            $post_commenter_image[$post_count][$count_comments[$post_count]]  = htmlentities($row['image_name']);
                        else
                            $post_commenter_image[$post_count][$count_comments[$post_count]]  = "user.png";
                $post_comment_content[$post_count][$count_comments[$post_count]] = htmlentities($row['post_comment_content']);
                $post_comment_date_commented[$post_count][$count_comments[$post_count]] = $row['date_commented'];
                if((time() - $row['date_commented']) >  86400)
                    $post_comment_date_commented[$post_count][$count_comments[$post_count]] =  gmdate("d-m-Y", $row['date_commented']);
                else
                    $post_comment_date_commented[$post_count][$count_comments[$post_count]] = splitTime(time() - $row['date_commented']);
                $post_comment_username[$post_count][$count_comments[$post_count]] = $row['username'];

                $count_comments[$post_count]++;
            }
        }


        $post_count++;
    }
}

$result = $conn->query($following_sql);
$row = mysqli_fetch_row($result);
$following = $row[0];

$sql = "SELECT users.user_id, users.username, following.user1_id, following.user2_id, following.date_followed
        FROM users
        INNER JOIN following ON users.user_id = following.user2_id
        WHERE following.user1_id=$user_id
        ORDER BY following.date_followed ASC;";

$result = $conn->query($sql);
$following_count = 0;

if($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $following_username[$following_count] = $row['username'];
        if((time() - $row['date_followed']) >  86400)
            $following_date[$following_count] =  gmdate("d-m-Y", $row['date_followed']);
        else
            $following_date[$following_count] = splitTime(time() - $row['date_followed']);
        $following_count++;
    }
}

$sql = "SELECT * FROM following WHERE user1_id=? AND user2_id=?";

$ps = $conn->prepare($sql);
$ps->bind_param("ii", $session_user_id, $user_id);
$ps->execute();
$result = $ps->get_result();

if ($result->num_rows > 0) {
    $currently_follow = true;
}
else
    $currently_follow = false;

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
    <link rel="stylesheet" type="text/css" href="/css/user.css">
    <link rel="stylesheet" type="text/css" href="/css/home.css">
</head>
<body>
    <?php include($_SERVER["DOCUMENT_ROOT"]."/include/nav.php"); ?>
    <div class="banner">
        <?php
                echo "<img class='profile-picture' src='/img/user-images/$user_image'>";
        ?>
    </div>
    <div class="main-content margined">
        <h1><?php echo $firstname." ".$lastname; ?></h1>
        <p><?php echo $bio ?></p>
        <?php
        if($session_user_id != null && $same_user == false) {
            echo "<div class='button-container'>";
            if($currently_follow == true)
                echo "<form id=\"follow-form\" action=\"/forms/follow.php\" method=\"post\">
                        <input type='hidden' name='user_id' value='$user_id'>
                        <input type=\"button\" value=\"FOLLOWING\" class=\"follow-button profile-button following\" type=\"submit\">
                    </form>";
            else
                echo "<form id=\"follow-form\" action=\"/forms/follow.php\" method=\"post\">
                        <input type='hidden' name='user_id' value='$user_id'>
                        <input type=\"button\" value=\"FOLLOW\" class=\"follow-button profile-button\" type=\"submit\">
                    </form>";
            echo "<div class='profile-button message-button'>Message</div>";
            echo "</div>";
        }
        ?>
    </div>
    <div class="numbers-container">
        <div class="margined">
            <div id="followers" class="box">
                <p><?php echo $followers ?></p>
                <p class="label">Followers</p>
                <?php echo "<form id=\"get-followers\" action=\"/forms/profile-data.php\" method=\"post\">
                    <input type='hidden' name='user_id' value='$user_id'>
                    <input type='hidden' name='type' value='followers'>
                </form>"; ?>
            </div>
            <div id="following" class="box">
                <p><?php echo $following ?></p>
                <p class="label">Following</p>
                <?php echo "<form id=\"get-following\" action=\"/forms/profile-data.php\" method=\"post\">
                    <input type='hidden' name='user_id' value='$user_id'>
                    <input type='hidden' name='type' value='following'>
                </form>"; ?>
            </div>
            <div id="posts" class="box box-selected">
                <p><?php echo $total_posts ?></p>
                <p class="label">Posts</p>
                <?php echo "<form id=\"get-posts\" action=\"/forms/profile-data.php\" method=\"post\">
                    <input type='hidden' name='user_id' value='$user_id'>
                    <input type='hidden' name='type' value='posts'>
                </form>"; ?>
            </div>
            <div id="likes" class="box">
                <p><?php echo $total_likes ?></p>
                <p class="label">Likes</p>
                <?php echo "<form id=\"get-likes\" action=\"/forms/profile-data.php\" method=\"post\">
                    <input type='hidden' name='user_id' value='$user_id'>
                    <input type='hidden' name='type' value='likes'>
                </form>"; ?>
            </div>
            <div id="shares" class="box">
                <p><?php echo $total_shares ?></p>
                <p class="label">Shares</p>
                <?php echo "<form id=\"get-shares\" action=\"/forms/profile-data.php\" method=\"post\">
                    <input type='hidden' name='user_id' value='$user_id'>
                    <input type='hidden' name='type' value='shares'>
                </form>"; ?>
            </div>
        </div>
    </div>
    <div class="lower-sec">
        <div class="margined">
            <?php

            for($i = 0;$i < $post_count; $i++) {
                echo "<div class=\"post post-$post_id[$i]\">
                                <div class=\"top-bar\">
                                    <div class=\"user-info\">
                                        <img src=\"/img/user-images/$user_image\">
                                        <div class=\"user-name\">
                                            <p>$firstname $lastname</p>
                                            <a href='/user/".strtolower($username)."'><span>@$username</span></a>
                                        </div>
                                    </div>
                                    <div class=\"right-info\">
                                        <p>$post_date_posted[$i]</p>";
                if($user_id == $session_user_id || $session_user_id == 1)
                    echo "<span class='delete-post' onclick='deletePost($session_user_id, $post_id[$i])'><i class='far fa-trash-alt'></i></span>";
                echo "</div>
                                    <div class=\"clear\"></div>
                                </div>
                                <div class=\"post-body\">
                                    <p>$post_content[$i]</p>
                                </div>
                                <div class=\"post-interactions\">";


                if($user_likes_post[$i] == false)
                    echo "<div class=\"button like no-select\" onclick='interaction(1, $post_id[$i])'>
                                            <span class='above redtext'>".($post_likes[$i] + 1)."</span>
                                            <span class='interaction-text'>LIKE</span>
                                            <span class='center'>$post_likes[$i]</span>
                                        </div>";
                else
                    echo "<div class=\"button like no-select\" onclick='interaction(1, $post_id[$i])'>
                                            <span class='above redtext' style='top:5px'>$post_likes[$i]</span>
                                            <span class='interaction-text redtext'>LIKE</span>
                                            <span class='center' style='top:100%'>".($post_likes[$i] - 1)."</span>
                                        </div>";

                if($user_shares_post[$i] == false)
                    echo "<div class=\"button share no-select\" onclick='interaction(2, $post_id[$i])'>
                                            <span class='above redtext'>".($post_shares[$i] + 1)."</span>
                                            <span class='interaction-text'>SHARE</span>
                                            <span class='center'>$post_shares[$i]</span>
                                        </div>";
                else
                    echo "<div class=\"button share no-select\" onclick='interaction(2, $post_id[$i])'>
                                            <span class='above redtext' style='top:5px'>$post_shares[$i]</span>
                                            <span class='interaction-text redtext'>SHARE</span>
                                            <span class='center' style='top:100%'>".($post_shares[$i] - 1)."</span>
                                        </div>";
                echo "<div class=\"reply-form\">
                                            <input type=\"text\" placeholder=\"Reply\" name='comment'>
                                            <div onclick='comment(".$post_id[$i].", $session_user_id, \"$session_user_username\")' class='comment-button'>POST</div>
                                        </div>
                                </div>
                                <div class='comment-body'>
                                <h2 id='postnum-$i'>Comments <span style='font-size:14px'>(</span><span id='comment-count-".$post_id[$i]."'>".$count_comments[$i]."</span><span style='font-size:14px'>)</span><i class=\"fas fa-caret-down\"></i></h2>";
                for($c = 0; $c < $count_comments[$i]; $c++) {
                    echo "<div class='comment-container postnum-$i' id='comment-".$post_comment_id[$i][$c]."'>
                                            <img src='/img/user-images/".$post_commenter_image[$i][$c]."'>
                                            <p><a href='/user/".strtolower($post_comment_username[$i][$c])."' class='author'>@".$post_comment_username[$i][$c]."</a><span class='time'>".$post_comment_date_commented[$i][$c]."</span>".$post_comment_content[$i][$c]."</p>
                                            <div class='clear'></div>";
                    if($post_commenter_id[$i][$c] == $session_user_id || $session_user_id == 1)
                        echo "<span class='delete-comment' onclick='deleteComment($session_user_id, ".$post_comment_id[$i][$c].", ".$comment_post_id[$i][$c].")'><i class=\"far fa-trash-alt\"></i></span>";
                    echo "</div>";
                }
                echo "<div class='clear'></div></div>
                            </div>";
            }
            if($post_count == 0)
                echo "<h2 class='no-posts'>No posts available</h2><script>$('.lower-sec').css('background', '#fff');</script>";

            ?>
        </div>
    </div>
    <?php
    if($session_user_id == null || $same_user == true)
        echo "<script> $('.lower-sec').css('padding-bottom', '100px'); </script>";
    ?>
</body>
<script src="/script/nav.js"></script>
<script src="/script/user.js"></script>
<script src="/script/home.js"></script>
</html>