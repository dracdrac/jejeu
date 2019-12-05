<?php    
    // PAGE : UN JEU
    require('template/header.php');
    echo affiche_page_jeu($_GET['id']);
    $lien_modifier = generer_lien_modifier('jeux', $_GET['id']);
    require('template/footer.php');
?>
