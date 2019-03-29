$(".plus").click(function() {
    id = this.id;
    if($(this).hasClass("minus")) {
        $(this).removeClass("minus");
        $("#q" + id + " > .question-body").css({"max-height": "0", "opacity", "0"});
    } else {
        $(this).addClass("minus");
        $("#q" + id + " > .question-body").css({"max-height": "100px", "opacity", "1"});
    }
})