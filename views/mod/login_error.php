<div id="register">
    <div class="message">Неправильный логин или пароль</div>

    <form action="{base_url}mod/login" method="post" accept-charset="utf-8">

        <div class="text">Ваш email</div>
        <input type="text" class="input-text input-recover" name="s_login" value="{value_email}" />
        <div class="clear"></div>

        <div class="text">Ваш пароль</div>
        <input type="password" class="input-text input-recover" name="s_password" value="{value_password}" />
        <div class="clear"></div>

        <div class="button button-login"><input type="submit" name="login" value="Войти"/></div>

    </form>
</div>