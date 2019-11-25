<?php    
    // PAGE : UN JEU
    require('template/header.php');
    echo affiche_page_jeu($_GET['id']);
    $lien_modifier = "admin.php?action=modifierjeu&id=" . $_GET['id'];
    require('template/footer.php');
?>
