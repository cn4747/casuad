<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>{title}</title>
    <link href="{base_url}css/style.css" type="text/css" rel="stylesheet"/>
    <script type="text/javascript" src="{base_url}js/jquery.js"></script>
    <script type="text/javascript" src="{base_url}js/jquery.cycle.all.min.js"></script>
    <script type="text/javascript" src="{base_url}js/script.js"></script>
    {upload_video_scripts}
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Cache-Control" CONTENT="no-cache" />
    <base href="{base_url}"></base>
</head>

<body>

<div id="outer">
<div id="inner">

<div id="header">
    <div class="left">

        <div class="logo">
            <a href="{base_url}"><img src="images/logo.png" alt=""/></a>
        </div><!-- .logo -->

        <div class="search">
            <form action="{base_url}search" method="post">
                <input type="text" name="text" class="search-field" value="Поиск" autocomplete="off"/>
                <div class="search-button"><input type="submit" name="search" class="search-button" value=""/></div>
            </form>
            <div id="search-hints"></div>
        </div><!-- .search -->

    </div><!-- .left-->

    {header_block}

</div><!-- #header -->
