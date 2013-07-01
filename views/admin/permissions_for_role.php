<p>Здесь выставляются права для конкретной группы на конкретный модуль.<br/>
    Делается, если нужно показывать в сайдбаре админки разные модули
    разным группам пользователей</p>

<p>Группа &laquo;<b><?php echo $role_name; ?>&raquo;</b></p>

<?php if(isset($message)) echo "<p class='message'>$message</p>"; ?>

<table border="0" cellpadding="0" cellspacing="0" class="modules">
    <tr>
        <td>Отображать</td>
        <td>Название модуля</td>
        <td>Имя файла</td>
        <td></td>
    </tr>
    <?php foreach($module_list as $item): ?>
        <tr>
        <?php echo form_open($base_url . 'admin/permissions/' . $role_name); ?>
            <td><?php
                echo form_checkbox('perm', '1', (bool)$item['perm']);
                echo form_hidden('file', $item['file']);
            ?></td>
            <td><?php echo $item['name']; ?></td>
            <td><?php echo $item['file']; ?></td>
            <td><input type="submit" name="update" value="Применить"/></td>
        <?php echo form_close(); ?>
        </tr>
    <?php endforeach; ?>
</table>