<p>Добавить группу:</p>

<?php if(isset($message)) echo "<div class='message'>{$message}</div>"; ?>

<table border="0" class="add_user">
<?php echo form_open($base_url . 'add'); ?>
    <tr>
        <td>
            <label>Метка</label>
            <p><?php echo form_input('name'); ?></p>
        </td>
        <td>
            <label>Название</label>
            <p><?php echo form_input('title'); ?></p>
        </td>
    </tr>
    <tr>
        <td>
            <label>Описание</label>
            <p><?php echo form_input('descr'); ?></p>
        </td>
        <td>
            <?php echo form_submit('add', 'Добавить'); ?>
        </td>
    </tr>
<?php echo form_close(); ?>
</table>