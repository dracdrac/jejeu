<?php    
    // PAGE : UN ARTICLE
    require('template/header.php');
    echo affiche_page_article($_GET['id']);
    $lien_modifier = generer_lien_modifier('articles',  $_GET['id']);
    require('template/footer.php');
?>
