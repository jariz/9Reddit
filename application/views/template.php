<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/html">
<head>
    <title>9Reddit</title>
    <script src="http://static.jariz.pro/js/jquery.min.js"></script>
    <script src="<?=base_url()?>static/js/le.js"></script>
    <script>
        var auth = "{auth}";
    </script>
    <link href="<?=base_url()?>static/css/le.css" type="text/css" rel="stylesheet">
    <link rel="shortcut icon" href="<?=base_url()?>favicon.ico" />
</head>
<body>
    <header>
        <div class="bar-content">
            <a href="<?=base_url()?>" class="brand">9REDDIT</a>
            <p><? $e = $this->config->item("slogans"); echo $e[rand(0, count($e)-1)]?></p>
            <div class="right">
                {authorized}<p style="font-weight: normal;">Welcome back: <strong></strong>{name} (<strong>{link_karma} &#183; {comment_karma}</strong>)</p><a href="logout" class="btn btn-inverse">Logout</a> {/authorized}
                <input type="text" id="subreddit" placeholder="Enter a subreddit :)">
            </div>
        </div>
    </header>
    <div class="layout">
        <div class="content">
            <ul class="type-tab">
                {typeitem}<li{active}><a href="<?=base_url()?>{url}"><strong>{title}</strong></a></li>{/typeitem}
            </ul>
            {post}
            <article>
                {url}
                <section>
                    <p>{title}</p>
                    <a href="/u/{author}">{author}</a>
                    <h4><i class="icon-smile"></i>{upvotes} <i class="icon-comment"></i>{comments}</h4>
                    <ul class="voting">
                        <li><a href="javascript:void(0)" data-thing="{thing}"><span class="icon-unlike">Downvote</span></a></li>
                        <li><a href="javascript:void(0)" data-thing="{thing}"><span class="icon-like">Upvote</span></a></li>
                    </ul>
                </section>
            </article>
            {/post}
            {noposts}
        </div>
        <div class="sidebar">
            {notauthorized}<a class="btn btn-danger btn-block" href="<?=base_url()?>auth">Y U NO AUTHORIZE?!</a>{/notauthorized}
            <div class="box">
                <h4>Show your love for 9reddit</h4>
                <iframe src="http://ghbtns.com/github-btn.html?user=jariz&repo=9Reddit&type=fork" allowtransparency="true" frameborder="0" scrolling="0" width="62" height="20"></iframe>
                <a href="https://twitter.com/_JariZ" class="twitter-follow-button" data-show-count="false" data-show-screen-name="false" data-dnt="true"></a>
                <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
                <a href="http://www.reddit.com/submit?url=<?=urlencode(current_url())?>"> <img src="http://www.reddit.com/static/spreddit1.gif" alt="submit to reddit" border="0" /> </a>
                <form style="display: inline-block" action="https://www.paypal.com/cgi-bin/webscr" method="post">
                    <input type="hidden" name="cmd" value="_s-xclick">
                    <input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
">
                    <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                    <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
                </form>
            </div>
            <div class="box green">
                <h4>Before you get all mad and stuff...</h4>
                Note that this is a parody site, this is reddit with a 9gag layout. The frontpage automatically gets the last posts from yesterday, but you can also choose to get those of today
            </div>
            <div class="box blue">
                <h4>Recommended subreddits</h4>
                {button}<a href="/r/{name}" class="btn {class}">{name}</a> {/button}
            </div>
        </div>
    </div>
</body>
</html>