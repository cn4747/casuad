<?php if(isset($message)) echo "<div class='message'>{$message}</div>"; ?>

<?php echo form_open_multipart($base_url . 'profile', 'class="wide_form"'); ?>
    <label>Логин</label>
    <p><?php
        echo form_input('edit_login', $login);
        echo form_hidden('login', $login);
    ?></p>

    <label>E-Mail</label>
    <p><?php
        echo form_input('edit_email', $email);
        echo form_hidden('email', $email);
    ?></p>
    
    <label>Новый пароль</label>
    <p><?php echo form_password('edit_password1'); ?></p>

    <label>Новый пароль, еще раз</label>
    <p><?php echo form_password('edit_password2'); ?></p>

    <label>Аватар</label>
    <p><input type="file" name="edit_avatar"/></p>
    <p><?php if(isset($avatar)) echo $avatar; ?></p>

    <?php echo form_submit('save', 'Применить'); ?>
<?php echo form_close(); ?>
