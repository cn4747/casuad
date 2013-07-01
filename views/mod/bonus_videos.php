<div id="bonus-videos">
	{video_list}
    <div class="list-item">
        <a class="title" href="{base_url}mod/video/{id}">{video_title}</a>
		<a href="{base_url}mod/video/{id}"><img src="{base_url}{thumb}" alt="" /></a>
		
        <div class="clear"></div>

        <div class="points">Баллы: {points}</div>
    </div><!-- .list-item -->
    {/video_list}

    {pagination}
</div>