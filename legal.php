<?php    
    $config = include('config.php');
    require_once $config['paths']['template'] . "/header.php";
    require_once $config['paths']['lib'] .  '/jeux.php';

    echo affiche_page_article(3);
    $lien_modifier = "admin.php?action=modifierarticle&id=3";

    require_once $config['paths']['template'] . "/footer.php";
?>