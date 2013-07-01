<h1>Управление рекламой</h1>

<a href="<?php echo $current_url; ?>upload">Загрузить баннер</a>

<?php if(is_array($banners)) foreach($banners as $item): ?>
<div class="banner-item">
    <img width="100px" src="<?php echo $base_url,'uploads/banners/',$item['image']; ?>" align="left" alt=""/>

    <p><b><?php echo $item['text1']; ?></b></p>
    <p><?php echo $item['text2']; ?></p>

    <p>
		<a href="<?php echo $current_url,'edit/',$item['id']; ?>">Редактировать</a>
        <a class="confirmDelete" href="<?php echo $current_url,'delete/',$item['id']; ?>">Удалить</a>
    </p>
</div><br/>
<?php endforeach; ?>