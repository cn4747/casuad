<?php if($this->session->userdata('user_id') !== FALSE): ?>
<div id="sidebar">
    <ul>
        <li><a href="<?php echo config_item('base_url'); ?>admin/panel">Панель управления</a></li>
        <?php if( ! empty($modules_enabled)): ?>
        <li>&nbsp;</li>
        <?php
            foreach($modules_enabled as $item)
            {
                $item['file'] = str_replace('.php', '', $item['file']);
                echo "<li><a href='" . config_item('base_url') .
                     "mod_admin/{$item['file']}'>{$item['name']}</a></li>";
            }
        ?>
        <li>&nbsp;</li>
        <?php endif; ?>

        <?php if($this->auth->is_admin() == TRUE): ?>
        <li><a href="<?php echo config_item('base_url'); ?>admin/manage">Управление модулями</a></li>
        <li><a href="<?php echo config_item('base_url'); ?>admin/permissions">Права групп для модулей</a></li>
        <?php endif; ?>
        <li><a href="<?php echo config_item('base_url'); ?>admin/logout">Выход</a></li>
    </ul>
</div>
<?php endif; ?>