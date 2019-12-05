<?php    
    // PAGE : UNE ETIQUETTE
    require('template/header.php');
    echo affiche_page_etiquette($_GET['id']);
    $lien_modifier = generer_lien_modifier('etiquettes', $_GET['id']);

    require('template/footer.php');
?>
