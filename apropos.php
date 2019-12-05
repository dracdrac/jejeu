<?php
    // PAGE : ABOUT
    require('template/header.php');
    echo affiche_page_article(1);
    $lien_modifier = generer_lien_modifier('articles', 1);
    require('template/footer.php');
?>

