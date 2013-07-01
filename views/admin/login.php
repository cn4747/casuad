
    <?php echo form_open(config_item('base_url') . 'admin/login', array('class'=>'login')); ?>
        <?php if(isset($errors)):?>
        <div class="errors"><?php echo $errors; ?></div>
        <?php endif; ?>
        
        <label>Логин:</label>
        <input type="text" name="login" value="<?php echo set_value('login'); ?>"/><br/>
        
        <label>Пароль:</label>
        <input type="password" name="password" value="<?php echo set_value('password'); ?>"/><br/><br/>

        <label class="remember">
            Запомнить?
            <input type="checkbox" name="remember" value="1" <?php echo set_checkbox('remember', '1'); ?>/>
        </label>
        
        <input type="submit" name="submit" value="Войти" class="submit"/>
    <?php echo form_close(); ?>
