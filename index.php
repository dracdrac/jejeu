<?php
    // PAGE : ACCUEIL
    require('template/header.php');
    echo affiche_page_article(2);
    $lien_modifier = "admin.php?action=modifierarticle&id=2";
    require('template/footer.php');
?>

