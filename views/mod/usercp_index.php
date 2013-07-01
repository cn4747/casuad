<div id="user-video-list">

    <div class="tabs">

        <a href="#">Моё видео</a>

        <div class="button"><a href="{base_url}mod/upload_video"></a></div>

    </div><!-- .tabs -->


    {myvideos}

</div><!-- #user-video-list -->


<div id="profile-data">
    <h1>Персональные данные</h1>

    <div class="data">
        <div class="ava">
            <img src="{base_url}/uploads/avatars/{avatar}" alt="" />
            <a href="{base_url}mod/usercp/picture">Изменить аватарку</a>
        </div><!-- .ava -->

        <div class="item">Ваш email</div>
        <div class="item-text">{user_email}</div>

        <div class="item">Никнэйм</div>
        <div class="item-text">{user_nick}</div>

        <div class="item">Пароль</div>
        <div class="item-text">******</div>

        <div class="button"><a href="{base_url}mod/usercp/data">Изменить данные</a></div>
    </div><!-- .data -->
</div><!-- #profile-data -->


<div id="bonus">
    <h1><a href="{base_url}{points_url}">Бонусные баллы</a></h1>

    <div class="points">Количество: {points_sum}</div>
    <div class="clear"></div>
    {points_block}
</div><!-- #bonus -->