
<?php    
    $config = include('config.php');
    require_once $config['paths']['template'] . "/header.php";
    require_once $config['paths']['lib'] .  '/jeux.php';
    echo affiche_page_article($_GET['id']);


    require_once $config['paths']['template'] . "/footer.php";
?>