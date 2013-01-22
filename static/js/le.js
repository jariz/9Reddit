
$(document).ready(function() {
    $("#subreddit").keydown(function(e) { if(event.which == 13) window.location.href = "/r/"+$(this).val(); });
    $(".voting li a").click( function() {
        if(auth == 0) window.location.href = "/auth";
        var parent = $(this).parent();
        $.each(parent.parent().children(), function() {
            if($(this).html() != parent.html())
                $(this).removeClass("clicked");
        });
        if(!parent.hasClass("clicked")) {
            parent.addClass("clicked");
            var span = $($(this).children("span")[0]);

            if(span.hasClass("icon-like")) {
                vote(1, $(this).attr("data-thing"));
            } else {
                vote(-1, $(this).attr("data-thing"));
            }
        }
        else {
            parent.removeClass("clicked");
            vote(0, $(this).attr("data-thing"));
        }
    })

    function vote(dir,thing) {
        $.get("/vote/"+dir+"/"+thing);
    }
})