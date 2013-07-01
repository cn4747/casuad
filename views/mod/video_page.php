{video_item}

<div id="video-list">
    
    <div class="tabs">
        
        <a href="#">Другое видео</a>
        
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
