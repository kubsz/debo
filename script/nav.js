$(".dropdown").hover(function() {
    $(".lower-dropdown").css("margin-top", "-1px");
});
$(".dropdown").mouseleave(function() {
    $(".lower-dropdown").css("margin-top", "-200px");
});