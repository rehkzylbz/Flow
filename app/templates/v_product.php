<?php
    echo '<h1 class="my-3">Flow - ', $cat['name'], '</h1>';
    echo '<h4 class="my-3">Товаров найдено: ', count($products), '</h4>';
    echo '<p>
        <a class="btn btn-primary my-1" href="categories" title="Вернуться в каталог" role="button">    
            Вернуться в каталог    
        </a>
    </p>';
    foreach ( $products as $product ) {
        echo '<div class="card mb-3" style="max-widtho: 300px;">
            <div class="row no-gutters">
                <div class="col-md-4 p-2">
                    <img class=" w-100" src="'.(!empty($product['img'])?$product['img']:'app/images/foto_not_found.jpg').'" alt="'.$product['name'].'" title="'.$product['name'].'">
                </div>
                <div class="col-md-8">
                    <div class="card-body">
                        <h5 class="card-title">'.$product['name'].'</h5>
                        <p class="card-text">'.$product['price'].' руб.</p>
                        <p class="card-text"><small class="text-muted">'.$product['descr'].'</small></p>
                    </div>
                </div>
            </div>
        </div>';
    };    