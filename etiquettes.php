<?php
    // PAGE : TOUTES LES ETIQUETTES
    require('template/header.php');
?>

<h2>Toutes les etiquettes</h2>
<div class='bddlist'>
    <?php echo affiche_liste_etiquettes(); ?>
</div>

<?php require('template/footer.php'); ?>

