$('#login-form > label').click(function() {
    var elem = $(this).attr('id');
    var input = elem.substring(0, elem.length - 6);
    $('#' + input).focus();
    $('#' + elem).addClass("label-up");
    $('#' + input).addClass("input-focused");
});
$('#login-form > input').focusin(function() {
    var elem = $(this).attr('id');
    console.log(elem);
    var label = elem + "-label";
    $('#' + elem).addClass("input-focused");
    $('#' + label).addClass("label-up");
});

$("#login-form > input").focusout(function(){
    if(!$(this).val()) {
        $('#' + this.id + '-label').removeClass("label-up");
        $('#' + this.id).removeClass("input-focused");
    }
});

$("button#log-button").click( function() {
    if(stop == true) return false;
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
    $("#login-form").submit( function() {
        return false;
    });
});