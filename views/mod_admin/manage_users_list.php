<?php if(isset($message)) echo "<p class='message'>{$message}</p>"; ?>

<p>Список пользователей группы <b><?php echo $role_title; ?></b>:</p>

<table border="0" class="modules">
    <tr>
        <td width="50px">Активен</td>
        <td>Ник</td>
        <td>E-Mail</td>
        <td>Новый пароль</td>
        <td>Группа</td>
        <td>&nbsp;</td>
    </tr>

    <?php foreach($user_list as $item): ?>
    <tr>
        <?php echo form_open($current_url); ?>
        <td><?php echo form_checkbox('active', '1', (bool)$item['active']); ?></td>
        <td>
            <abbr title="Создан <?php echo date('d.m.Y - H:i', $item['created']); ?>">
            <?php echo $item['nick']; ?>
            </abbr>
        </td>
        <td><?php echo form_input('email', $item['email']); ?></td>
        <td><?php echo form_input('password'); ?></td>
        <td><?php echo form_dropdown('role_id', $roles_list, $item['role_id']); ?></td>
        <td><?php
            echo form_hidden('user_id', $item['id']);
            echo form_submit('update', 'Применить');
            echo ' ';
            echo form_submit('delete', 'Удалить', 'class="confirmDelete"');
        ?></td>
        <?php echo form_close(); ?>
    </tr>
    <?php endforeach; ?>

</table>