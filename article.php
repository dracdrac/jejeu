<?php    
    // PAGE : UN ARTICLE
    require('template/header.php');
    echo affiche_page_article($_GET['id']);
    $lien_modifier = "admin.php?action=modifierarticle&id=" . $_GET['id'];
    require('template/footer.php');
?>
