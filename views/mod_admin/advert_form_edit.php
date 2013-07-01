<?php if( ! empty($message)) echo "<div class='message'>{$message}</div>"; ?>

<a href="<?php echo $base_url; ?>advert">&laquo; Назад</a><br/><br/>

<form action="" method="post" accept-charset="utf-8" enctype="multipart/form-data">
    <input type="hidden" name="a_id" value="<?php echo $a_id; ?>" />
	Текст 1<br/>
    <input type="text" name="text1" value="<?php echo $a_text1; ?>"/><br/><br/>

    Текст 2<br/>
    <input type="text" name="text2" value="<?php echo $a_text2; ?>"/><br/><br/>

    URL<br/>
    <input type="text" name="url" value="<?php echo $a_url; ?>"/><br/><br/>

    <input type="submit" name="add" value="Отправить"/>
</form>