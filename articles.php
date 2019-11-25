<?php
    // PAGE : TOUS LES ARTICLES
    require('template/header.php');
?>

<h2>Tous les articles</h2>
<div class='bddlist'>
    <?php echo affiche_liste_articles(); ?>
</div>

<?php require('template/footer.php'); ?>

