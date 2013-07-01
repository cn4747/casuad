<h1>Список видео:</h1>

<?php if(isset($message)) echo "<div class='message'>{$message}</div>"; ?>

<?php foreach($videos as $item):
    if( ! empty($item['link']))
        $img_link = $base_url . 'uploads/video_thumbs/mini_' . $item['link'] . '.jpg';
    else
        $img_link = $base_url . 'images/video_loading.jpg';
    ?>
<form action="" method="post" accept-charset="utf-8" class="video-list-item">
    <input type="hidden" name="id" value="<?php echo $item['id']; ?>"/>
    <img width="60px" src="<?php echo $img_link; ?>" alt="" align="left"/>&nbsp;
    <div class="video-hd">
        <a href="<?php echo $base_url; ?>mod/video/<?php echo $item['id']; ?>"><?php echo $item['video_title']; ?></a>
    </div>
    <input type="text" name="points" size="5" value="<?php echo $item['points']; ?>"/>
    <input type="submit" name="add" value="Добавить"/>
</form>
<?php endforeach; ?>

<?php echo $pagination; ?>