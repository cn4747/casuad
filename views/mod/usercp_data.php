<div id="content">
    <div class="video">
        <h1>Изменить данные</h1>
        
        <div id="register">
        <form action="" method="post" accept-charset="utf-8">

            {message}
            
            <div class="text">Email</div>
            <input type="text" class="input-text" name="email" value="{user_email}" />
            <input type="hidden" name="old_email" value="{user_email}" />
            <div class="clear"></div>
            
            <div class="text">Ник</div>
            <input type="text" class="input-text" name="nick" value="{user_nick}" />
            <input type="hidden" name="old_nick" value="{user_nick}" />
            <div class="clear"></div>
            
            <div class="text">Новый пароль</div>
            <input type="password" class="input-text" name="password1" value="" />
            <div class="clear"></div>
            
            <div class="text">Подтверждение пароля</div>
            <input type="password" class="input-text" name="password2" value="" />
            <div class="clear"></div>
            
            <div class="button button-margin"><input type="submit" name="change" value="Изменить данные"/></div>

        </form>
        </div><!-- #register -->
    </div><!-- .video -->
    
    {advert_block}
    
</div><!-- #content -->
