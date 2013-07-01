<h1>Выберите пользователя:</h1>

<?php foreach($users as $item): ?>
<a href="send_message/user/<?php echo $item['id']; ?>"><?php echo $item['nick']; ?></a><br/>
<?php endforeach; ?>

<?php echo $pagination; ?>