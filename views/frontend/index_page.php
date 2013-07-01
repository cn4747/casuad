<div id="content">
    <div class="video">
        <h1>{top_video_title}</h1>

        <p class="text">{top_video_descr}</p>

        <div class="youtube">
            <iframe width="640" height="405" src="http://www.youtube.com/embed/{top_video_link}" frameborder="0" allowfullscreen></iframe>
        </div>
    </div><!-- .video -->

    {advert_block}

</div><!-- #content -->

<div id="video-list">

    <div class="tabs">

        <a class="{tab_class_1}" href="{base_url}#video-list">Новое видео</a>
        <a class="{tab_class_2}" href="{base_url}main/rating#video-list">Рекомендуют</a>

    </div><!-- .tabs -->


    
    {video_list}
    <div class="list-item">
        <a href="{base_url}mod/video/{id}"><img src="{video_thumb}" alt="" /></a>

        <a href="{base_url}mod/video/{id}">{video_title}</a>

        <div class="time">Добавлено {time} назад</div>

        <div class="views">{views}</div>
    </div><!-- .list-item -->
    {/video_list}


    
    {pagination}

</div><!-- #video-list -->
