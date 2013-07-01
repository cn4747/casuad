<h1>Сообщения пользователю &laquo;<?php echo $username; ?>&raquo;</h1>

<?php if(isset($message)) echo "<div class='message'>{$message}</div>"; ?>

<form action="" method="post" accept-charset="utf-8" id="message">
    Тема<br/>
    <input type="text" class="title" name="title"/><br/><br/>

    Сообщение<br/>
    <textarea rows="5" name="body"></textarea><br/><br/>

    <input type="submit" name="send" value="Отправить"/>
</form>

<?php if(is_array($messages)) foreach($messages as $item): ?>
<p class="message-item">
    <h2><?php echo $item['message_title']; ?></h2>
    <p><?php echo $item['body']; ?></p>
    <p>
        Дата: <small><?php echo date('d.m.Y - H:i:s', $item['date']); ?></small> &nbsp;&nbsp;&nbsp;
        <a class="confirmDelete" href="<?php echo $base_url; ?>delete/<?php echo $item['id'],'/',$user_id; ?>">Удалить</a>
    </p>
</p>
<?php endforeach; ?>