<div id="content">
    <div class="video">
        <h1>Изменить аватарку</h1>
        
        <div id="register">
        <form action="" method="post" accept-charset="utf-8" enctype="multipart/form-data">

            {message}
            
            <img src="{base_url}uploads/avatars/{avatar}" alt="" />
            <div class="clear"></div>
            
            <div class="text">Выберите изображение</div>
            <input type="file" class="input-text" name="avatar"/>
            <div class="clear"></div>
            
            <div class="button button-margin"><input type="submit" name="change" value="Изменить аватарку"/></div>

        </form>
        </div><!-- #register -->
    </div><!-- .video -->
    
    {advert_block}
    
</div><!-- #content -->
