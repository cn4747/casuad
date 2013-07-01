
    <?php if(isset($message)) echo "<p class='message'>{$message}</p>"; ?>

    <?php if( ! empty($modules)): ?>
    <table border="0" cellpadding="0" cellspacing="0" class="modules">
        <tr>
            <td>Название модуля</td>
            <td>Имя файла</td>
            <td></td>
        </tr>
    <?php foreach($modules as $item): ?>
        <tr>
        <?php echo form_open(config_item('base_url') . 'admin/manage/' . $type, array('class'=>'login')); ?>
            <td>
            <?php
                echo form_hidden('file', $item['file']);
                $value = array(
                    'value' => $item['name'],
                    'size' => 50,
                    'name' => 'name'
                );
                echo form_input($value);
            ?></td>
            <td><?php echo $item['file']; ?></td>
            <td>
                <input type="submit" name="update" value="Применить"/>
                <input type="submit" name="delete" value="Удалить из базы"/>
            </td>
        <?php echo form_close(); ?>
        </tr>
    <?php endforeach; ?>
    </table>
    <?php else: ?>
    <p>Папка с модулями пуста</p>
    <?php endif; ?>
