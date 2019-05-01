$(".create-post-button").prop('disabled', true);

$(".create-post-textarea").keyup(function(e){
    $("#character-count").text((256 - $(this).val().length) + " characters remaining");
    if($(this).val().length == 0) {
        $(".create-post-button").addClass("not-allowed");
        $(".create-post-button").prop('disabled', true);
    }
    if($(this).val().length > 0) {
        $(".create-post-button").removeClass("not-allowed");
        $(".create-post-button").prop('disabled', false);

    } else if($(this).val().length == 256) {
        e.stopImmediatePropagation();
        return;
    }else if($(this).val().length > 256) {
        this.value = this.value.substring(0, 256);
        $("#character-count").text("0 characters remaining");
    }
});



function interaction(interaction, post_id) {
    $.post("/forms/interact.php", {interaction: interaction, post_id: post_id })
        .done(function (data) {
            if(data == "n") {
                return false;
            }
            if(data == "1w") {
                $(".post-" + post_id + " .like .above").css("top", "5px");
                $(".post-" + post_id + " .like .center").css("top", "37.5px");
                $(".post-" + post_id + " .like .interaction-text").addClass("redtext");
            } else if(data == "1f") {
                $(".post-" + post_id + " .like .above").css("top", "-37.5px");
                $(".post-" + post_id + " .like .center").css("top", "5px");
                $(".post-" + post_id + " .like .interaction-text").removeClass("redtext");
            } else if(data == "2w") {
                $(".post-" + post_id + " .share .above").css("top", "5px");
                $(".post-" + post_id + " .share .center").css("top", "37.5px");
                $(".post-" + post_id + " .share .interaction-text").addClass("redtext");
            } else if(data == "2f") {
                $(".post-" + post_id + " .share .above").css("top", "-37.5px");
                $(".post-" + post_id + " .share .center").css("top", "5px");
                $(".post-" + post_id + " .share .interaction-text").removeClass("redtext");
            }
            console.log(data);
        });
}

function comment(post_id, user_id, username) {
    var comment = $(".post-" + post_id + " .reply-form").find('input[name="comment"]').val();
    $.post("/forms/comment.php", {comment: comment, post_id: post_id, user_id: user_id })
        .done(function (data) {
            if(data == "n") {
                $(".post-" + post_id + " input").css({"border-color": "#d10606"})
            } else {
                var params = data.split(',');
                $(".post-" + post_id + " .comment-body").append("<div class='comment-container' id='comment-" + params[1] + "'><img src='/img/user-images/user.png'><p><a href='/users.php?user=" + username + "' class='author'>@" + username + "</a><span class='time'>1 second ago</span>" + comment + "</p><div class='clear'></div><span class='delete-comment' onclick='deleteComment(" + params[0] + ", " + params[1] + ", " + params[2] + ")'><i class=\"far fa-trash-alt\"></i></span></div>");
                $(".post-" + post_id + " .reply-form > input").val('');
                $("#comment-count-" + post_id).text(Number($("#comment-count-" + post_id).text()) + 1);
            }
        });
}

function deleteComment(user_id, post_comment_id, post_id) {
    $.post("/forms/delete-comment.php", {user_id: user_id, post_comment_id: post_comment_id })
        .done(function (data) {
            $("#comment-" + post_comment_id).css("display", "none");
            $("#comment-count-" + post_id).text(Number($("#comment-count-" + post_id).text()) - 1);
        });
}
function deletePost(user_id, post_id) {
    $.post("/forms/delete-post.php", {user_id: user_id, post_id: post_id })
        .done(function (data) {
            if(data == "true")
                $(".post-" + post_id).css("display", "none");
        });
}

$(".comment-body > h2").click(function() {
    num = this.id;
    if($("." + num).css("display") == "block") {
        $("#" + num + " > i").css("transform", "rotate(180deg)");
        $("." + num).css("display", "none");
    }
    else {
        $("." + num).css("display", "block");
        $("#" + num + " > i").css("transform", "rotate(0deg)");
    }
});

$(".fa-trash-alt").mouseover(function() {
    $(this).removeClass("far");
    $(this).addClass("fas");
});
$(".fa-trash-alt").mouseout(function() {
    $(this).removeClass("fas");
    $(this).addClass("far");
});

$(".header input").keyup(function() {
    var val = $(this).val();
    $.post("/forms/get-users.php", { search: val })
        .done(function (data) {
            $(".results").html(data);
        });
});