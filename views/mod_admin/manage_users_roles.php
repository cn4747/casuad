<p>Выберите группу пользователей</p>

<p><a href="<?php echo $base_url . 'add'; ?>"><b>Добавить пользователя</b></a></p>

<?php foreach($role_list as $item): ?>
<p>
    <a href="<?php echo $base_url; ?>role/<?php echo $item['name']; ?>"><?php echo $item['title']; ?></a><br/>
    <?php echo $item['descr']; ?>
</p>
<?php endforeach; ?>
