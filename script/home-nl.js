$("input").attr('autocomplete', 'new-password');
function resetModal(type) {
    $("." + type + "-modal").css("transform", "translateY(-200%)");
    $("#" + type + "-modal-background").css("background", "transparent");
    setTimeout(function(){
        $("#" + type + "-modal-background").css("display", "none");
    },300);
}

$(".register").click(function() {
    $("#register-modal-background").css("display", "block");
    setTimeout(function(){
        $("#register-modal-background").css("background", "rgba(0,0,0,.7)");
        $(".register-modal").css("transform", "translateY(0)");
    },1);
});
$(".login").click(function() {
    $("#login-modal-background").css("display", "block");
    setTimeout(function(){
        $("#login-modal-background").css("background", "rgba(0,0,0,.7)");
        $(".login-modal").css("transform", "translateY(0)");
    },1);
});

reg_modal_bg = document.getElementById("register-modal-background");
log_modal_bg = document.getElementById("login-modal-background");

window.onclick = function(e) {
    if (e.target == reg_modal_bg) {
        resetModal("register");
    }
    else if (e.target == log_modal_bg) {
        resetModal("login");
    }
}

function switchModal(current, next) {
    resetModal(current);
    setTimeout(function(){
        $("." + next).trigger('click');
    },400);
}

var a = false; var b = false; var c = false; var d = false; var e = false;
$('#register-form > span').click(function() {
    var elem = $(this).attr('id');
    var input = elem.substring(0, elem.length - 6);
    $('#' + input).focus();
    $('#' + elem).addClass("label-up");
    $('#' + input).addClass("input-focused");
});
$('#register-form > input').focusin(function() {
    var elem = $(this).attr('id');
    var label = elem + "-label";
    $('#' + elem).addClass("input-focused");
    $('#' + label).addClass("label-up");
});

$('#login-form > span').click(function() {
    var elem = $(this).attr('id');
    var input = elem.substring(0, elem.length - 6);
    $('#' + input).focus();
    $('#' + elem).addClass("label-up");
    $('#' + input).addClass("input-focused");
    console.log(elem);
});
$('#login-form > input').focusin(function() {
    var elem = $(this).attr('id');
    var label = elem + "-label";
    $('#' + elem).addClass("input-focused");
    $('#' + label).addClass("label-up");
});

var valid = 0;

$("#register-form > input").focusout(function(e){
    if(!$(this).val()) {
        $('#' + this.id + '-label').removeClass("label-up");
        $('#' + this.id).removeClass("input-focused");
    }
    var input = this.id;
    var regex  = /[^a-z\d]/i;

    if($(this).val().length == 0) {
        $('#' + input).removeClass("invalid-input");
        $('#' + input).removeClass("valid-input");
        $('#' + input + "-label").removeClass("invalid-label");
        $('#' + input + "-label").removeClass("valid-label");
        $('#' + input + "-icon").removeClass("icon-show");
        return;
    }
    switch (input) {
        case "username":
            if(regex.test($(this).val()) || $(this).val().length == 0 || $(this).val().length < 3 || $(this).val().length > 16 && $(this).val().length != 0) {
                $('#' + input).removeClass("valid-input");
                $('#' + input).addClass("invalid-input");
                $('#' + input + "-label").removeClass("valid-label");
                $('#' + input + "-label").addClass("invalid-label");
                $('#' + input + "-icon").addClass("icon-show");
                $('#' + input + "-icon").removeClass("fa-check");
                $('#' + input + "-icon").addClass("fa-times");
                valid = 2;
            }
            break;
        case "email":
            var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            if (!filter.test($(this).val()) || $(this).val().length < 3 || $(this).val().length > 48 && $(this).val().length != 0) {
                $('#' + input).removeClass("valid-input");
                $('#' + input).addClass("invalid-input");
                $('#' + input + "-label").removeClass("valid-label");
                $('#' + input + "-label").addClass("invalid-label");
                $('#' + input + "-icon").addClass("icon-show");
                $('#' + input + "-icon").removeClass("fa-check");
                $('#' + input + "-icon").addClass("fa-times");
                return;
            }
            break;
        case "password":
            if ($(this).val().length < 6 || $(this).val().length > 64) {
                $('#' + input).removeClass("valid-input");
                $('#' + input).addClass("invalid-input");
                $('#' + input + "-label").removeClass("valid-label");
                $('#' + input + "-label").addClass("invalid-label");
                $('#' + input + "-icon").addClass("icon-show");
                $('#' + input + "-icon").removeClass("fa-check");
                $('#' + input + "-icon").addClass("fa-times");
                return;
            }
            break;
        case "first-name":
            if (regex.test($(this).val()) || $(this).val().length == 1) {
                $('#' + input).removeClass("valid-input");
                $('#' + input).addClass("invalid-input");
                $('#' + input + "-label").removeClass("valid-label");
                $('#' + input + "-label").addClass("invalid-label");
                $('#' + input + "-icon").addClass("icon-show");
                $('#' + input + "-icon").removeClass("fa-check");
                $('#' + input + "-icon").addClass("fa-times");
                return;
            }
            break;
        case "last-name":
            if (regex.test($(this).val()) || $(this).val().length == 1) {
                $('#' + input).removeClass("valid-input");
                $('#' + input).addClass("invalid-input");
                $('#' + input + "-label").removeClass("valid-label");
                $('#' + input + "-label").addClass("invalid-label");
                $('#' + input + "-icon").addClass("icon-show");
                $('#' + input + "-icon").removeClass("fa-check");
                $('#' + input + "-icon").addClass("fa-times");
                return;
            }
            break;
    }
    $('#' + input).removeClass("invalid-input");
    $('#' + input).addClass("valid-input");

    $('#' + input + "-label").removeClass("invalid-label");
    $('#' + input + "-label").addClass("valid-label");

    $('#' + input + "-icon").addClass("icon-show");
    $('#' + input + "-icon").removeClass("fa-times");
    $('#' + input + "-icon").addClass("fa-check");
    valid++;
    console.log(valid);

    if(valid >= 5) {
        $(".reg-button").removeClass("button-invalid");
    }
    else
        $(".reg-button").addClass("button-invalid");
});

$(".reg-button").click( function() {
    if(valid < 4)
        return false;
    $.post( $("#register-form").attr("action"),
        $("#register-form :input").serializeArray(),
        function(data) {
            if(data == "true") {
                window.location.href = "/";
                return false;
            }
            $("#reg-error-text").text(data);
            $("#reg-error-text").css("opacity", "1");
        });
    $("#register-form").submit( function() {
        return false;
    });
});

var logvalid = 0;
$("#login-form > input").focusout(function(e){
    if(!$(this).val()) {
        $('#' + this.id + '-label').removeClass("label-up");
        $('#' + this.id).removeClass("input-focused");
    }
    var input = this.id;
    var regex  = /[^a-z\d]/i;

    if($(this).val().length == 0) {
        $('#' + input).removeClass("invalid-input");
        $('#' + input).removeClass("valid-input");
        $('#' + input + "-label").removeClass("invalid-label");
        $('#' + input + "-label").removeClass("valid-label");
        $('#' + input + "-icon").removeClass("icon-show");
        return;
    }
    switch (input) {
        case "log-username":
            if(regex.test($(this).val()) || $(this).val().length == 0 || $(this).val().length < 3 || $(this).val().length > 16 && $(this).val().length != 0) {
                $('#' + input).removeClass("valid-input");
                $('#' + input).addClass("invalid-input");
                $('#' + input + "-label").removeClass("valid-label");
                $('#' + input + "-label").addClass("invalid-label");
                $('#' + input + "-icon").addClass("icon-show");
                $('#' + input + "-icon").removeClass("fa-check");
                $('#' + input + "-icon").addClass("fa-times");
                return;
            }
            break;
        case "log-password":
            if ($(this).val().length < 6 || $(this).val().length > 64) {
                $('#' + input).removeClass("valid-input");
                $('#' + input).addClass("invalid-input");
                $('#' + input + "-label").removeClass("valid-label");
                $('#' + input + "-label").addClass("invalid-label");
                $('#' + input + "-icon").addClass("icon-show");
                $('#' + input + "-icon").removeClass("fa-check");
                $('#' + input + "-icon").addClass("fa-times");
                return;
            }
            break;
    }
    $('#' + input).removeClass("invalid-input");
    $('#' + input).addClass("valid-input");
    $('#' + input + "-label").removeClass("invalid-label");
    $('#' + input + "-label").addClass("valid-label");
    $('#' + input + "-icon").addClass("icon-show");
    $('#' + input + "-icon").removeClass("fa-times");
    $('#' + input + "-icon").addClass("fa-check");
    logvalid++;

    if(logvalid >= 2) {
        $(".log-button").removeClass("button-invalid");
    }
    else
        $(".log-button").addClass("button-invalid");
});

$(".log-button").click( function() {
    if(logvalid < 1)
        return false;
    $.post( $("#login-form").attr("action"),
        $("#login-form :input").serializeArray(),
        function(data) {
            if(data == "true") {
                window.location.href = "/";
                return false;
            }
            $("#log-error-text").text(data);
            $("#log-error-text").css("opacity", "1");
            $(".login-modal").css("height", "330px");
        });
    $("#login-form").submit( function() {
        return false;
    });
});
$(document).ready(function() {
    $("#row-1").css({"background": "#0096C8", "box-shadow": "3px 10px 19px rgba(0, 0, 0, 0.29)"});
    $("#row-1 p").css("color", "#fff");
    $("#row-1 h3").css("color", "#fff");
})
count = 2;
window.setInterval(function(){
    console.log(count);
    var color = ["#0096C8", "#5829c5", "#f36b21"];

    $(".row").css({"background": "transparent", "box-shadow": "3px 10px 19px rgba(0, 0, 0, 0)"});
    $(".row p").css("color", "#000");
    $("#row-1 h3").css("color", color[0]);
    $("#row-2 h3").css("color", color[1]);
    $("#row-3 h3").css("color", color[2]);

    $("#row-" + count).css({"background": color[count - 1], "box-shadow": "3px 10px 19px rgba(0, 0, 0, 0.29)"});
    $("#row-" + count + " p").css("color", "#fff");
    $("#row-" + count + " h3").css("color", "#fff");

    if(count == 1) {
        setTimeout(function(){
            $(".phone").css({"display": "none", "transform": "scale(.3)"});
            setTimeout(function(){
                $(".phone").css("display", "block");
                $("#phone-screen").attr("src", "https://i.imgur.com/9Tw5RRI.png");
                setTimeout(function(){
                    $(".phone").css({"transform": "scale(1)", "opacity": "1"});
                },20);
            },20);
        },10);
    }
    if(count == 2) {
        $(".phone").css({"display": "none", "transform": "scale(.3)", "opacity": "0"});
        $("#shield").css("display", "block");
        setTimeout(function(){
            $("#shield").css({"transform": "scale(1)", "opacity": "1"});
        },20);
    }
    if(count == 3) {
        $("#shield").css({"display": "none", "transform": "scale(.3)"});
        $(".phone").css("display", "block");
        $("#phone-screen").attr("src", "https://i.imgur.com/QHm8Ovm.png");
        setTimeout(function(){
            $(".phone").css({"transform": "scale(1)", "opacity": "1"});
        },20);
    }

    if(count == 3)
        count = 1;
    else
        count++;
}, 4000);