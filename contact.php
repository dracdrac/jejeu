<?php    
    // PAGE : CONTACT
    require('template/header.php');
    echo affiche_page_article(4);
    $lien_modifier = generer_lien_modifier('articles', 4);
    require('template/footer.php');
?>

