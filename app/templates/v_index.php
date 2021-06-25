<h1 class="my-3">Flow - главная</h1>
<p>
    <a class="btn btn-primary my-5" href="categories" title="Просмотреть список категорий" role="button">    
        Просмотреть список категорий    
    </a>
</p>
<?php 
      if (isset($_POST['post'])) { ?>
        <div class="alert alert-primary" role="alert">
            <?php 
                $api = new Api($flow_settings); 
                $result = $api::check_data();
                echo '<pre>', $result[0], '</pre>';
                echo '<pre>', 'Категории: ', $result[1]['Категории']['content'], '</pre>';
                echo '<pre>Продукты: ', $result[2]['Продукты']['content'], '</pre>';
            ?>    
        </div>
<?php } ?>

<form method="POST">
    <input type="hidden" name="post">
    <button type="submit" class="btn btn-outline-primary">Обновить данные в БД</button>
</form>