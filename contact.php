<?php    
    $config = include('config.php');
    require_once $config['paths']['template'] . "/header.php";
    require_once $config['paths']['lib'] .  '/jeux.php';

    echo affiche_page_article(4);
    $lien_modifier = "admin.php?action=modifierarticle&id=4";

    require_once $config['paths']['template'] . "/footer.php";
?>