<?php
    echo '<h1 class="my-3">Flow - категории</h1>';
    
    function render_cat($cat = [], $cats = []) {
        echo '<li class="list-group-item"><a href="product?cat_id=',$cat['cat_id'],'">', $cat['name'], ' (', $cat['products_count'], ')</a>';
        if ( isset($cats[(int)$cat['cat_id']]) ) {
            echo '<button class="btn btn-success btn-sm mx-3 pt-0 pb-1 float-right" type="button" data-toggle="collapse" data-target="#collapse_'.$cat['cat_id'].'" aria-expanded="false" aria-controls="collapse_'.$cat['cat_id'].'">
                    >
                </button>';
            echo '<ul class="list-group mt-2 collapse" id="collapse_'.$cat['cat_id'].'">';
            foreach ( $cats[(int)$cat['cat_id']] as $child_cat ) { 
                render_cat($child_cat, $cats);    
            };
            echo '</ul>';
        };
        echo '</li>';
    };
    
    echo '<ul class="list-group mt-4">';
    foreach ( $cats[0] as $cat ) {
        render_cat($cat, $cats);
    }; 
    echo '</ul>';