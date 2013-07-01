<?php if(isset($message)) echo "<div class='message'>{$message}</div>"; ?>

<p><a href="<?php echo $base_url; ?>add"><b>Добавить группу</b></a></p>

<p>Список групп:</p>

<table border="0" class="modules">
    <tr>
        <td>Метка</td>
        <td>Название группы</td>
        <td>Описание</td>
        <td>&nbsp;</td>
    </tr>

    <?php foreach($role_list as $item): ?>
    <tr>
        <?php echo form_open($base_url); ?>
        <td>
        <?php
            echo form_hidden('old_name', $item['name']);
            echo form_input('name', $item['name']);
        ?>
        </td>
        <td><?php echo form_input('title', $item['title']); ?></td>
        <td><?php
            $params = array(
                'name'      =>  'descr',
                'value'     =>  $item['descr'],
                'rows'      =>  '5',
                'cols'      =>  '30'
            );
            echo form_textarea($params); ?>
        </td>
        <td><?php
            echo form_hidden('id', $item['id']);
            echo form_submit('update', 'Применить');
            echo ' ';
            echo form_submit('delete', 'Удалить', 'class="confirmDelete"');
        ?></td>
        <?php echo form_close(); ?>
    </tr>
    <?php endforeach; ?>

</table>