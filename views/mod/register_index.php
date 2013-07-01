        <h1>Регистрация пользователя</h1>

        <p class="text">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris tempus semper consequat. Duis ut consequat erat.</p>

        <div id="register">
        <form action="{base_url}mod/register" method="post" accept-charset="utf-8">

            <div class="text-social-reg">Зарегистрироваться с помощью</div>

            <div class="social-buttons">
                <a class="facebook" href="http://loginza.ru/api/widget/?token_url=http%253A%252F%252Feto5.org%252Fsocial&provider=facebook">&nbsp;</a>
                <a class="twitter" href="http://loginza.ru/api/widget/?token_url=http%253A%252F%252Feto5.org%252Fsocial&provider=twitter">&nbsp;</a>
                <a class="gplus" href="http://loginza.ru/api/widget/?token_url=http%253A%252F%252Feto5.org%252Fsocial&provider=google">&nbsp;</a>
            </div><!-- .social -->
            <div class="clear"></div>

            {message}

            <div class="text">Ваш email</div>
            <input type="text" class="input-text" name="email" value="{value_email}" />
            <div class="clear"></div>

            <div class="text">Введите пароль</div>
            <input type="password" class="input-text" name="password1" value="{value_password1}" />
            <div class="clear"></div>

            <div class="text">Подтвердите пароль</div>
            <input type="password" class="input-text" name="password2" value="{value_password2}" />
            <div class="clear"></div>

            <div class="terms">Я принимаю <a href="{base_url}pages/rules">условия</a> сайта <input type="checkbox" name="accept" value="1" {value_accept}/></div>

            <div class="button"><input type="submit" name="register" value="Зарегистрироваться"/></div>

        </form>
        </div>
