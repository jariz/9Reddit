
$(document).ready(function() {
    $("#subreddit").keydown(function(e) { if(event.which == 13) window.location.href = "/r/"+$(this).val(); });
})