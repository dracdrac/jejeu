<?php    
    // PAGE : INFORMATION LEGALES
    require('template/header.php');
    echo affiche_page_article(3);
    $lien_modifier = "admin.php?action=modifierarticle&id=3";
    require('template/footer.php');
?>

