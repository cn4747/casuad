<p>Добавить пользователя:</p>

<?php if(isset($message)) echo "<div class='message'>{$message}</div>"; ?>

<table border="0" class="add_user">
<?php echo form_open($base_url . 'add'); ?>
    <tr>
        <td>
            <label>Ник</label>
            <p><?php echo form_input('ins_nick'); ?></p>
        </td>
        <td>
            <label>Пароль</label>
            <p><?php echo form_input('ins_password'); ?></p>
        </td>
    </tr>
    <tr>
        <td>
            <label>E-Mail (логин)</label>
            <p><?php echo form_input('ins_email'); ?></p>
        </td>
        <td>
            <label>Группа</label>
            <p><?php echo form_dropdown('ins_role', $roles_list) ?></p>
        </td>
    </tr>
    <tr>
        <td>
            <p>
                <label>Активировать? </label>
                <?php echo form_checkbox('ins_active', '1', TRUE); ?>
            </p>
        </td>
        <td>
            <?php echo form_submit('add', 'Добавить'); ?>
        </td>
    </tr>
<?php echo form_close(); ?>
</table>