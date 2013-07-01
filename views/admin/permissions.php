
    <p>Выберите группу, для которой нужно установить особые права на модули</p>

    <p>
<?php

    $base = config_item('base_url');

    foreach($roles_list as $item)
    {
        echo "<a href='{$base}admin/permissions/{$item['name']}'>{$item['title']}</a><br/>";
    }
?>
    </p>
