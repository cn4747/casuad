        <h1>Восстановление пароля</h1>

        <p>Введите регистрационный email в форму ниже. На него будет выслан новый пароль</p>

        <div id="register">
        <form action="{base_url}mod/register/recover" method="post" accept-charset="utf-8">

            {message}

            <div class="text text-recover">Ваш email</div>
            <input type="text" class="input-text input-recover" name="email" value="{value_email}" />

            <div class="button button-recover"><input type="submit" name="recover" value="Отправить"/></div>

        </form>
        </div>
