<?php    
    // PAGE : INFORMATION LEGALES
    require('template/header.php');
    echo affiche_page_article(3);
    $lien_modifier = generer_lien_modifier('articles', 3);
    require('template/footer.php');
?>

