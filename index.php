<?php
include($_SERVER['DOCUMENT_ROOT']."/include/checkuser.php");

function parseContent($string) {
    $split = explode('@', $string)[1]; //elliot memerson does!
    $name = explode(' ', $split)[0]; //elliot memerson does!
    $namelink = "<a class='link' href='/user/$name'>@$name</a>";
    $newstring = str_replace("@".$name, $namelink, $string);
    return $newstring;
}

if(!isset($_COOKIE['logsession'])) {
    include($_SERVER['DOCUMENT_ROOT']."/homepage.php");
    die();
}
else {
    $logged_in = true;

    $sql = "SELECT * FROM following WHERE user1_id=$session_user_id";
    $count_followers = 0;

    $result = $conn->query($sql);
    if($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $following_id[$count_followers] = $row['user2_id'];
            $count_followers++;
        }
    }

    $sql = "SELECT *
        FROM ((posts
        INNER JOIN users ON posts.author_id = users.user_id)
        INNER JOIN user_images ON users.user_id = user_images.user_id)
        WHERE author_id=$session_user_id";
    for($i = 0; $i < $count_followers; $i++) {
        if($count_followers > 0)
            $sql = $sql." OR";
        if($i == ($count_followers - 1))
            $sql = $sql." author_id=$following_id[$i] ORDER BY posts.date_posted DESC";
        else
            $sql = $sql." author_id=$following_id[$i]";
    }
}
$count_post = 0;
$result = $conn->query($sql);

if($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $post_id[$count_post] = $row['post_id'];
        $post_content[$count_post] = parseContent(htmlentities($row['content']));
        $post_author_username[$count_post] = htmlentities($row['username']);
        if($row['image_name'] != null)
            $post_author_image[$count_post]  = htmlentities($row['image_name']);
        else
            $post_author_image[$count_post]  = "user.png";
        $post_author_id[$count_post] = $row['user_id'];

        if((time() - $row['date_posted']) >  86400)
            $post_date_created[$count_post] =  gmdate("d-m-Y", $row['date_posted']);
        else
            $post_date_created[$count_post] = splitTime(time() - $row['date_posted']);


        $post_author_first_name[$count_post] = htmlentities($row['first_name']);
        if($post_author_first_name[$count_post] == null)
            $post_author_first_name[$count_post] = "John";

        $post_author_last_name[$count_post] = htmlentities($row['last_name']);
        if($post_author_last_name[$count_post] == null)
            $post_author_last_name[$count_post] = "Doe";

        $likes_sql = "SELECT COUNT(*) FROM interactions WHERE post_id=".$row['post_id']." AND interaction=1";
        $likes_result = $conn->query($likes_sql);
        $likes_row = mysqli_fetch_row($likes_result);
        $likes[$count_post] = $likes_row[0];

        $shares_sql = "SELECT COUNT(*) FROM interactions WHERE post_id=".$row['post_id']." AND interaction=2";
        $shares_result = $conn->query($shares_sql);
        $shares_row = mysqli_fetch_row($shares_result);
        $shares[$count_post] = $shares_row[0];

        $user_likes_post_sql = "SELECT * FROM interactions WHERE user_id=$session_user_id AND post_id=".$row['post_id']." AND interaction=1";
        $user_likes_post_result = $conn->query($user_likes_post_sql);
        if($user_likes_post_result->num_rows > 0)
            $user_likes_post[$count_post] = true;
        else
            $user_likes_post[$count_post] = false;

        $user_shares_post_sql = "SELECT * FROM interactions WHERE user_id=$session_user_id AND post_id=".$row['post_id']." AND interaction=2";
        $user_shares_post_result = $conn->query($user_shares_post_sql);
        if($user_shares_post_result->num_rows > 0)
            $user_shares_post[$count_post] = true;
        else
            $user_shares_post[$count_post] = false;

        $comments_sql = "SELECT * FROM ((post_comments
        INNER JOIN users ON post_comments.post_commenter_id = users.user_id)
        INNER JOIN user_images ON users.user_id = user_images.user_id)
        WHERE post_id=$post_id[$count_post]
        ORDER BY date_commented ASC";
        $comments_result = $conn->query($comments_sql);
        $count_comments[$count_post] = 0;

        if($comments_result->num_rows > 0) {
            while($row = $comments_result->fetch_assoc()) {
                $post_comment_id[$count_post][$count_comments[$count_post]] = $row['post_comment_id'];
                $comment_post_id[$count_post][$count_comments[$count_post]] = $row['post_id'];
                $post_commenter_id[$count_post][$count_comments[$count_post]] = $row['post_commenter_id'];
                if($row['image_name'] != null)
                    $post_commenter_image[$count_post][$count_comments[$count_post]]  = htmlentities($row['image_name']);
                else
                    $post_commenter_image[$count_post][$count_comments[$count_post]]  = "user.png";
                $post_comment_content[$count_post][$count_comments[$count_post]] = parseContent(htmlentities($row['post_comment_content']));
                $post_comment_date_commented[$count_post][$count_comments[$count_post]] = $row['date_commented'];
                if((time() - $row['date_commented']) >  86400)
                    $post_comment_date_commented[$count_post][$count_comments[$count_post]] =  gmdate("d-m-Y", $row['date_commented']);
                else
                    $post_comment_date_commented[$count_post][$count_comments[$count_post]] = splitTime(time() - $row['date_commented']);
                $post_comment_username[$count_post][$count_comments[$count_post]] = $row['username'];

                $count_comments[$count_post]++;
            }
        }

        $count_post++;
    }
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
    <title>DEBO - Activity Feed</title>
    <link rel="stylesheet" type="text/css" href="/css/home.css">
    <?php include($_SERVER["DOCUMENT_ROOT"]."/include/head.php"); ?>
</head>
<body>
    <?php include($_SERVER["DOCUMENT_ROOT"]."/include/nav.php"); ?>
    <img class="city" src="/img/city.jpg">
    <div class="section blue sec1">
        <div class="margined">
            <h2 class="post-header">Create a Post</h2>
            <form action="/forms/create-post.php" method="post">
                <span id="character-count"></span>
                <textarea placeholder="What's on your mind?" class="create-post-textarea" name="content"></textarea>
                <input class="create-post-button not-allowed" type="submit" value="Post">
                <div class="clear"></div>
            </form>
        </div>
    </div>
    <div class="section">
        <div class="margined activity-feed">
            <div class="header">
                <h1>Activity Feed</h1>
                <div class="input-container">
                    <input placeholder="Search for users..." id="user-search">
                    <div class="results">

                    </div>
                </div>
            </div>
            <?php
                if($count_post == 0)
                    echo "<p style='text-align:center;font-size:32px;padding-top:100px;text-transform:uppercase;letter-spacing:2px;'>Search for users to follow in the input box!</p>";
                for($i = 0;$i < $count_post; $i++) {
                    echo "<div class=\"post post-$post_id[$i]\">
                            <div class=\"top-bar\">
                                <div class=\"user-info\">
                                    <img src=\"/img/user-images/".$post_author_image[$i]."\"><div class=\"user-name\">
                                        <p>$post_author_first_name[$i] $post_author_last_name[$i]</p>
                                        <a href='/user/".strtolower($post_author_username[$i])."'><span>@$post_author_username[$i]</span></a>
                                    </div>
                                </div>
                                <div class=\"right-info\">
                                    <p>$post_date_created[$i]</p>";
                    if($post_author_id[$i] == $session_user_id || $session_user_id == 1)
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
                                        <span class='above redtext'>".($likes[$i] + 1)."</span>
                                        <span class='interaction-text'>LIKE</span>
                                        <span class='center'>$likes[$i]</span>
                                    </div>";
                            else
                                echo "<div class=\"button like no-select\" onclick='interaction(1, $post_id[$i])'>
                                        <span class='above redtext' style='top:5px'>$likes[$i]</span>
                                        <span class='interaction-text redtext'>LIKE</span>
                                        <span class='center' style='top:100%'>".($likes[$i] - 1)."</span>
                                    </div>";

                                if($user_shares_post[$i] == false)
                                    echo "<div class=\"button share no-select\" onclick='interaction(2, $post_id[$i])'>
                                        <span class='above redtext'>".($shares[$i] + 1)."</span>
                                        <span class='interaction-text'>SHARE</span>
                                        <span class='center'>$shares[$i]</span>
                                    </div>";
                                else
                                    echo "<div class=\"button share no-select\" onclick='interaction(2, $post_id[$i])'>
                                        <span class='above redtext' style='top:5px'>$shares[$i]</span>
                                        <span class='interaction-text redtext'>SHARE</span>
                                        <span class='center' style='top:100%'>".($shares[$i] - 1)."</span>
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

            ?>
        </div>
    </div>
</body>
<script src="/script/nav.js"></script>
<script src="/script/home.js"></script>
<script>
    $("#home-option").addClass('active-nav');
</script>
</html>