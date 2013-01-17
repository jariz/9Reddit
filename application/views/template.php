<!DOCTYPE html>
<html>
<head>
    <title>9Reddit</title>
    <link href="<?=base_url()?>static/css/le.css" type="text/css" rel="stylesheet">
</head>
<body>
    <header>
        <div class="bar-content">
            <a href="<?=base_url()?>" class="brand">9REDDIT</a>

            <p>The frontpage of the internet yesterday</p>
        </div>
    </header>
    <div class="layout">
        <div class="content">
            <ul class="type-tab">
                <li class="active"><a href="<?=base_url()?>">What's old</a></li>
            </ul>
        </div>
        <div class="sidebar">
            <div class="box">
                <h3>Show your love for 9reddit</h3>
                <iframe src="http://ghbtns.com/github-btn.html?user=jariz&repo=9Reddit&type=fork" allowtransparency="true" frameborder="0" scrolling="0" width="62" height="20"></iframe>
                <a href="https://twitter.com/_JariZ" class="twitter-follow-button" data-show-count="false" data-show-screen-name="false" data-dnt="true"></a>
                <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
                <a href="http://www.reddit.com/submit?url=<?=urlencode(current_url())?>"> <img src="http://www.reddit.com/static/spreddit7.gif" alt="submit to reddit" border="0" /> </a>
                <p>Note that this is a parody site, this is reddit with a 9gag layout. The frontpage automatically gets the last posts from yesterday, but you can also choose to get those of today</p>
            </div>
        </div>
    </div>
</body>
</html>