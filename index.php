<?php
    //dev settings
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    
    $flow_settings = include 'settings.php';
	spl_autoload_register(function ($class_name) {
        include_once 'app/classes/'.$class_name.'.php';
    });	
    
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <link rel="stylesheet" href="app/css/bootstrap.min.css">
    
    <title>Flow</title>
</head>
<body>
    <div class="container">
        
<?php
    $uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    $path = trim(substr($uri, strpos($flow_settings['base_url'], $uri)+strlen($flow_settings['base_url'])), '/');
    if ( $path === '' ) {
        include_once 'app/templates/v_index.php';    
    }
    else if ( $path === 'categories' ) {
        $db = new DB();
        $cats = $db->get_all_cats();
        include_once 'app/templates/v_cats.php';    
    } 
    else if ( $path === 'product' ) {
        $db = new DB();
        $cat = $db->get_cat_by_id((int)$_GET['cat_id']);
        $products = $db->get_products_by_cat((int)$_GET['cat_id']);
        include_once 'app/templates/v_product.php';    
    }
    else 
        include_once 'app/templates/v_404.php';
?>

    </div>

    <script src="app/js/jquery-3.5.1.min.js"></script>
    <script src="app/js/popper.min.js"></script>
    <script src="app/js/bootstrap.min.js"></script>

</body>
</html>