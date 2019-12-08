<?php
    // PAGE : ACCUEIL
    require('template/header.php');
    echo affiche_page_article(2);
    $lien_modifier = generer_lien_modifier('articles', 2);

    require('template/footer.php');
?>

