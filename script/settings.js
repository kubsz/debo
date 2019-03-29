$("#change-email").click(function() {
    $(".inner-modal").html("<h2>Change Email Address</h2><h4>Current Email: <span class='blue-text'>" + email + "</span></h4><br><label>new email address*</label><input id='change-email-input' type='text' placeholder='Enter Your New Email' name='email'><span id='email-error-text' class='error-text'></span><i id='email-error-icon' class='error-icon far fa-times-circle'></i><button class='submit-button' onclick='changeEmail()'>SUBMIT</button>");

    openModal(255);
});

$("#change-username").click(function() {
    $(".inner-modal").html("<h2>Change Username</h2><h4>Current Username: <span class='blue-text'>" + username + "</span></h4><br><label>new username*</label><input id='change-username-input' type='text' placeholder='Enter Your New Username' name='username'><span id='username-error-text' class='error-text'></span><i id='username-error-icon' class='error-icon far fa-times-circle'></i><button class='submit-button' onclick='changeUsername()'>SUBMIT</button>");

    openModal(255);
})

$("#change-password").click(function() {
    $(".inner-modal").html("<h2>Change Username</h2><h4>Current Username: <span class='blue-text'>" + username + "</span></h4><br><label>current password*</label><input id='current-password-input' type='password' placeholder='Enter Your Current Password' name='current-password'><span id='current-password-error-text' class='error-text'></span><i id='current-password-error-icon' class='error-icon far fa-times-circle'></i><label>new password*</label><input id='new-password-input' type='password' placeholder='Enter Your New Password' name='new-password'><span id='new-password-error-text' class='error-text'></span><i id='new-password-error-icon' class='error-icon far fa-times-circle'></i><label>confirm new password*</label><input id='confirm-password-input' type='password' placeholder='Confirm Your New Password' name='confirm-password'><span id='confirm-password-error-text' class='error-text'></span><i id='confirm-password-error-icon' class='error-icon far fa-times-circle'></i><button class='submit-button' onclick='changePassword()'>SUBMIT</button>");

    openModal(430);
})

function resetModal() {
    $(".modal").css("transform", "translateY(-200%)");
    $(".modal-background").css("background", "transparent");
    setTimeout(function(){
        $(".modal-background").css("display", "none");
    },300);
}
function openModal(height) {
    $(".modal").css("transition-duration", "0s");
    setTimeout(function(){
        $(".modal-background").css("display", "block");
        $(".modal").css("height", height + "px");
        setTimeout(function(){
            $(".modal").css("transition-duration", "1s");
            setTimeout(function(){
                $(".modal-background").css("background", "rgba(0,0,0,.7)");
                $(".modal").css("transform", "translateY(0)");
            },10);
        },10);
    },10);
}

modal_bg = document.getElementById("modal-background");

window.onclick = function(e) {
    if (e.target == modal_bg) {
        resetModal();
    }
}

function changeEmail() {
    var new_email = document.getElementById('change-email-input').value;

    if(new_email.length < 4 || new_email.length > 16) {
        $("#change-email-input").addClass('error-border');
        $("#email-error-icon").css("display","block");
        invalidInput("Email Address is not valid", "email");
        return false;
    }
    $("#change-email-input").removeClass('error-border');
    $("#email-error-icon").css("display","none");
    $("#email-error-text").css("display","none");

    $.post("/forms/change-email.php", {email: new_email})
        .done(function (data) {
            if(data == "true") {
                validInput("Email Address updated!", "email");
            } else {
                invalidInput(data, "email");
            }
        });
}

function changeUsername() {
    var new_username = document.getElementById('change-username-input').value;

    if(new_username.length < 3 || new_username.length > 16) {
        $("#change-username-input").addClass('error-border');
        $("#username-error-icon").css("display","block");
        invalidInput("Username is not valid", "username");
        return false;
    }
    $("#change-username-input").removeClass('error-border');
    $("#username-error-icon").css("display","none");
    $("#username-error-text").css("display","none");

    $.post("/forms/change-username.php", {username: new_username})
        .done(function (data) {
            if(data == "true") {
                validInput("Username updated!", "username");
            } else {
                invalidInput(data, "username");
            }
        });
}

function changePassword() {
    var current_password = document.getElementById('current-password-input').value;
    var new_password = document.getElementById('new-password-input').value;
    var confirm_password = document.getElementById('confirm-password-input').value;

    $.post("/forms/change-password.php", {current_password: current_password, new_password: new_password, confirm_password: confirm_password })
        .done(function (data) {
            console.log(data);
        });
}

function invalidInput(error, type) {
    $("#" + type + "-error-icon").addClass('fa-times-circle');
    $("#" + type + "-error-icon").removeClass('fa-check-circle');

    $("#" + type + "-error-text").css({"display":"block", "color": "#dd000a"});
    $("#" + type + "-error-icon").css({"display":"block", "color": "#dd000a"});

    $("#change-" + type + "-input").addClass('error-border');
    $("#change-" + type + "-input").removeClass('valid-border');

    $("#" + type + "-error-text").html(error);
}
function validInput(text, type) {
    $("#" + type + "-error-icon").removeClass('fa-times-circle');
    $("#" + type + "-error-icon").addClass('fa-check-circle');

    $("#" + type + "-error-text").css({"display":"block", "color": "green"});
    $("#" + type + "-error-icon").css({"display":"block", "color": "green"});

    $("#change-" + type + "-input").addClass('valid-border');
    $("#change-" + type + "-input").removeClass('error-border');

    $("#" + type + "-error-text").html(text);
}