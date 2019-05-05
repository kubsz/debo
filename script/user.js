$(".follow-button").click( function() {
    $.post( $("#follow-form").attr("action"),
        $("#follow-form :input").serializeArray(),
        function(data) {
            console.log(data);
            if(data == "unfollow") {
                $(".follow-button").removeClass("following");
                $(".follow-button").prop("value", "FOLLOW");
                console.log("UNFOLLOW PLZ");
            }
            else if(data == "error") {
                return false;
            }
            else {
                $(".follow-button").addClass("following");
                $(".follow-button").prop("value", "FOLLOWING");
            }
        });
    $("#follow-form").submit( function() {
        return false;
    });
});

$(".copy-link-button").click(function() {
    var url = document.createElement('input'),
        text = window.location.href;

    document.body.appendChild(url);
    url.value = text;
    url.select();
    document.execCommand('copy');
    document.body.removeChild(url);
});

$(".box").click(function() {
    id = this.id;
    $.post( $("#get-" + id).attr("action"),
        $("#get-" + id + " :input").serializeArray(),
        function(data) {
            setTimeout(function(){
                $(".box").removeClass("box-selected");
                $("#" + id).addClass("box-selected");
                $(".lower-sec > .margined").css({"display": "none", "transition": ".1s ease all", "opacity": "0"});
                setTimeout(function(){
                    $(".lower-sec > .margined").html(data);
                    $(".lower-sec > .margined").css("display", "block");
                    setTimeout(function(){
                        $(".lower-sec > .margined").css("opacity", "1");
                    },1);
                },10);
            },100);
        });
});
