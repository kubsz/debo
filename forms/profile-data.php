<?php
include($_SERVER['DOCUMENT_ROOT']."/include/checkuser.php");

$type = $_POST['type'];
$user_id = $_POST['user_id'];

switch($type) {
    case "followers":

        $sql = "SELECT users.user_id, users.username, following.user1_id, following.user2_id, following.date_followed, user_images.image_name
        FROM ((users
        INNER JOIN following ON users.user_id = following.user1_id)
        LEFT JOIN user_images ON following.user1_id = user_images.user_image_id)
        WHERE following.user2_id=?
        ORDER BY following.date_followed ASC;";

        $ps = $conn->prepare($sql);
        $ps->bind_param("i", $user_id);
        $ps->execute();
        $result = $ps->get_result();

        $followers_count = 0;
        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $followers_username[$followers_count] = $row['username'];
                if((time() - $row['date_followed']) >  86400)
                    $followers_date[$followers_count] =  gmdate("d-m-Y", $row['date_followed']);
                else
                    $followers_date[$followers_count] = splitTime(time() - $row['date_followed']);

                if($row['image_name'] != null)
                    $followers_image[$followers_count]  = htmlentities($row['image_name']);
                else
                    $followers_image[$followers_count]  = "user.png";


                $followers_count++;
            }
        }
        if($followers_count > 0) {
            for($i = 0; $i < $followers_count; $i++) {
                if($i == 0)
                    echo "<div style='padding-top:15px'></div>";

                //changed from: <img src=\"/img/user-images/".$followers_image[$i]."\"><div class=\"user-name\">

                echo "<a href='/user/".strtolower($followers_username[$i])."'>
                        <div class=\"following-card\">
                            <img src=\"/img/user-images/".$followers_image[$i]."\">
                            <div class=\"following-content\">
                                <h4>@$followers_username[$i]</h4>
                                <p>Following since $followers_date[$i]</p>
                            </div>
                        </div>
                    </a>";

            }
        }
        else {
            echo "<h2 class='no-posts'>No Following available</h2>";
        }

        break;
    case "following":

        $sql = "SELECT users.user_id, users.username, following.user1_id, following.user2_id, following.date_followed, user_images.image_name
        FROM ((users
        INNER JOIN following ON users.user_id = following.user2_id)
        LEFT JOIN user_images ON following.user2_id = user_images.user_id)
        WHERE following.user1_id=?
        ORDER BY following.date_followed ASC;";

        $ps = $conn->prepare($sql);
        $ps->bind_param("i", $user_id);
        $ps->execute();
        $result = $ps->get_result();

        $following_count = 0;
        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $following_username[$following_count] = $row['username'];
                if((time() - $row['date_followed']) >  86400)
                    $following_date[$following_count] =  gmdate("d-m-Y", $row['date_followed']);
                else
                    $following_date[$following_count] = splitTime(time() - $row['date_followed']);

                if($row['image_name'] != null)
                    $following_image[$following_count]  = htmlentities($row['image_name']);
                else
                    $following_image[$following_count]  = "user.png";

                $following_count++;
            }
        }
        if($following_count > 0) {
            for($i = 0; $i < $following_count; $i++) {
                if($i == 0)
                    echo "<div style='padding-top:15px'></div>";

                echo "<a href='/user/".strtolower($following_username[$i])."'>
                        <div class=\"following-card\">
                            <img src=\"/img/user-images/".$following_image[$i]."\">
                            <div class=\"following-content\">
                                <h4>@$following_username[$i]</h4>
                                <p>Following since $following_date[$i]</p>
                            </div>
                        </div>
                    </a>";
            }
        }
        else {
            echo "<h2 class='no-posts'>No Followers available</h2>";
        }

        break;
    case "posts":
        $sql = "SELECT *
        FROM ((users
        INNER JOIN posts ON users.user_id = posts.author_id)
        LEFT JOIN user_images ON posts.author_id = user_images.user_id)
        WHERE posts.author_id=?
        ORDER BY posts.date_posted DESC;";

        $ps = $conn->prepare($sql);
        $ps->bind_param("i", $user_id);
        $ps->execute();
        $result = $ps->get_result();

        $post_count = 0;
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $post_id[$post_count] = $row['post_id'];
                $post_content[$post_count] = htmlentities($row['content']);
                if((time() - $row['date_posted']) >  86400)
                    $post_date_posted[$post_count] =  gmdate("d-m-Y", $row['date_posted']);
                else
                    $post_date_posted[$post_count] = splitTime(time() - $row['date_posted']);

                $firstname[$post_count] = htmlentities($row['first_name']);
                $username[$post_count] = htmlentities($row['username']);
                if($firstname[$post_count] == null)
                    $firstname[$post_count] = "John";
                $lastname[$post_count] = htmlentities($row['last_name']);
                if($lastname[$post_count] == null)
                    $lastname[$post_count] = "Doe";

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

                $comments_sql = "SELECT * FROM ((post_comments
                INNER JOIN users ON post_comments.post_commenter_id = users.user_id)
                LEFT JOIN user_images ON users.user_id = user_images.user_image_id)
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

                if($row['image_name'] != null)
                    $user_image = htmlentities($row['image_name']);
                else
                    $user_image = "user.png";

                $post_count++;
            }
        }
        for($i = 0;$i < $post_count; $i++) {
            echo "<div class=\"post post-$post_id[$i]\">
                    <div class=\"top-bar\">
                        <div class=\"user-info\">
                            <img src=\"/img/user-images/".$user_image."\">
                            <div class=\"user-name\">
                                <p>$firstname[$i] $lastname[$i]</p>
                                <a href='//".strtolower($username[$i])."'><span>@$username[$i]</span></a>
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
                        <img src=\"/img/user-images/".$post_commenter_image[$i][$c]."\">
                        <p><a href='//".strtolower($post_comment_username[$i][$c])."' class='author'>@".$post_comment_username[$i][$c]."</a><span class='time'>".$post_comment_date_commented[$i][$c]."</span>".$post_comment_content[$i][$c]."</p>
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
        break;
    case "likes":
        $int = 1;

        $sql = "SELECT *
        FROM (((posts
        INNER JOIN interactions ON posts.post_id = interactions.post_id)
        INNER JOIN users ON posts.author_id = users.user_id)
        LEFT JOIN user_images ON user_images.user_image_id = posts.author_id)
        WHERE interactions.user_id=? AND interactions.interaction=?
        ORDER BY interactions.date_of_interaction DESC;";

        $ps = $conn->prepare($sql);
        $ps->bind_param("ii", $user_id, $int);
        $ps->execute();
        $result = $ps->get_result();

        $post_count = 0;
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $post_id[$post_count] = $row['post_id'];
                $post_content[$post_count] = htmlentities($row['content']);
                $username[$post_count] = htmlentities($row['username']);

                 if($row['image_name'] != null)
                    $user_likes_post_author_image[$post_count] = htmlentities($row['image_name']);
                else
                    $user_likes_post_author_image[$post_count] = "user.png";

                $firstname[$post_count] = htmlentities($row['first_name']);
                if($firstname[$post_count] == null)
                    $firstname[$post_count] = "John";
                $lastname[$post_count] = htmlentities($row['last_name']);
                if($lastname[$post_count] == null)
                    $lastname[$post_count] = "Doe";
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

                $comments_sql = "SELECT * FROM ((post_comments
                INNER JOIN users ON post_comments.post_commenter_id = users.user_id)
                LEFT JOIN user_images ON users.user_id = user_images.user_image_id)
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
        for($i = 0;$i < $post_count; $i++) {
            echo "<div class=\"post post-$post_id[$i]\">
                                <div class=\"top-bar\">
                                    <div class=\"user-info\">
                                        <img src=\"/img/user-images/".$user_likes_post_author_image[$i]."\">
                                        <div class=\"user-name\">
                                            <p>$firstname[$i] $lastname[$i]</p>
                                            <a href='//".strtolower($username[$i])."'><span>@$username[$i]</span></a>
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
                                            <p><a href='//".strtolower($post_comment_username[$i][$c])."' class='author'>@".$post_comment_username[$i][$c]."</a><span class='time'>".$post_comment_date_commented[$i][$c]."</span>".$post_comment_content[$i][$c]."</p>
                                            <div class='clear'></div>";
                if($post_commenter_id[$i][$c] == $session_user_id || $session_user_id == 1)
                    echo "<span class='delete-comment' onclick='deleteComment($session_user_id, ".$post_comment_id[$i][$c].", ".$comment_post_id[$i][$c].")'><i class=\"far fa-trash-alt\"></i></span>";
                echo "</div>";
            }
            echo "<div class='clear'></div></div>
                            </div>";
        }
        if($post_count == 0)
            echo "<h2 class='no-posts'>No likes available</h2><script>$('.lower-sec').css('background', '#fff');</script>";
        break;
    case "shares":
        $int = 2;

        $sql = "SELECT *
        FROM posts
        INNER JOIN interactions ON posts.post_id = interactions.post_id
        INNER JOIN users ON posts.author_id = users.user_id
        LEFT JOIN user_images ON user_images.user_image_id = posts.author_id
        WHERE interactions.user_id=? AND interactions.interaction=?
        ORDER BY interactions.date_of_interaction DESC;";

        $ps = $conn->prepare($sql);
        $ps->bind_param("ii", $user_id, $int);
        $ps->execute();
        $result = $ps->get_result();

        $post_count = 0;
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $post_id[$post_count] = $row['post_id'];
                $post_content[$post_count] = htmlentities($row['content']);
                $username[$post_count] = htmlentities($row['username']);
                if($row['image_name'] != null)
                    $user_shares_post_author_image[$post_count] = htmlentities($row['image_name']);
                else
                    $user_shares_post_author_image[$post_count] = "user.png";

                $firstname[$post_count] = htmlentities($row['first_name']);
                if($firstname[$post_count] == null)
                    $firstname[$post_count] = "John";
                $lastname[$post_count] = htmlentities($row['last_name']);
                if($lastname[$post_count] == null)
                    $lastname[$post_count] = "Doe";
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

                $comments_sql = "SELECT * FROM ((post_comments
                INNER JOIN users ON post_comments.post_commenter_id = users.user_id)
                LEFT JOIN user_images ON users.user_id = user_images.user_image_id)
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
        for($i = 0;$i < $post_count; $i++) {
            echo "<div class=\"post post-$post_id[$i]\">
                                <div class=\"top-bar\">
                                    <div class=\"user-info\">
                                        <img src=\"/img/user-images/".$user_shares_post_author_image[$i]."\">
                                        <div class='user-name'>
                                            <p>$firstname[$i] $lastname[$i]</p>
                                            <a href='//".strtolower($username[$i])."'><span>@$username[$i]</span></a>
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
                                            <p><a href='//".strtolower($post_comment_username[$i][$c])."' class='author'>@".$post_comment_username[$i][$c]."</a><span class='time'>".$post_comment_date_commented[$i][$c]."</span>".$post_comment_content[$i][$c]."</p>
                                            <div class='clear'></div>";
                if($post_commenter_id[$i][$c] == $session_user_id || $session_user_id == 1)
                    echo "<span class='delete-comment' onclick='deleteComment($session_user_id, ".$post_comment_id[$i][$c].", ".$comment_post_id[$i][$c].")'><i class=\"far fa-trash-alt\"></i></span>";
                echo "</div>";
            }
            echo "<div class='clear'></div></div>
                            </div>";
        }
        if($post_count == 0)
            echo "<h2 class='no-posts'>No shares available</h2><script>$('.lower-sec').css('background', '#fff');</script>";
        break;

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