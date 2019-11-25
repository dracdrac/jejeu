<?php
    // PAGE : ABOUT
    require('template/header.php');
    echo affiche_page_article(1);
    $lien_modifier = "admin.php?action=modifierarticle&id=1";
    require('template/footer.php');
?>

