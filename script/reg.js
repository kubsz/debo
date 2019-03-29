$('#register-form > span').click(function() {
    var elem = $(this).attr('id');
    var input = elem.substring(0, elem.length - 6);
    $('#' + input).focus();
    $('#' + elem).addClass("label-up");
    $('#' + input).addClass("input-focused");
});
$('#register-form > input').focusin(function() {
    var elem = $(this).attr('id');
    console.log(elem);
    var label = elem + "-label";
    $('#' + elem).addClass("input-focused");
    $('#' + label).addClass("label-up");
});

$("#register-form > input").focusout(function(){
    if(!$(this).val()) {
        $('#' + this.id + '-label').removeClass("label-up");
        $('#' + this.id).removeClass("input-focused");
    }
});

var stop = false;

function validateReg() {
    var username = document.getElementById("username").value;
    var email = document.getElementById("email").value;
    var password = document.getElementById("password").value;
    var password2 = document.getElementById("password2").value;
    var errormessage = "";
    stop = false;

    if(password != password2)
        errormessage = "passwords are not the same";

    if(password.length < 6)
        errormessage = "password is too short.";
    else if(password.length > 64)
        errormessage = "password is too long.";

    var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if (!filter.test(email))
        errormessage = "email is invalid.";

    if(username.length < 3)
        errormessage = "username is too short.";
    else if(username.length > 32)
        errormessage = "username is too long.";

    if(errormessage != "") {
        document.getElementById("error-text").innerHTML = errormessage;
        document.getElementById("error-text").style.opacity = "1";
        stop = true;
    }
}

$("button#reg-button").click( function() {
    validateReg();
    if(stop == true) return false;
    $.post( $("#register-form").attr("action"),
        $("#register-form :input").serializeArray(),
        function(data) {
            if(data == "true") {
                window.location.href = "/";
                return false;
            }
            $("#error-text").text(data);
            $("#error-text").css("opacity", "1");
        });
    $("#register-form").submit( function() {
        return false;
    });
});