<?php    
    // PAGE : UN JEU
    require('template/header.php');
    echo affiche_page_jeu($_GET['id']);
    $lien_modifier = "admin.php?action=update&categorie=jeux&id=" . $_GET['id'];
    require('template/footer.php');
?>
