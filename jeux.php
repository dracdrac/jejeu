<?php    
    $config = include('config.php');
    require_once $config['paths']['lib'] .  '/jeux.php';
    require_once $config['paths']['template'] . "/header.php";

?>
    <h2>Tous les jeux</h2>

    <div class='bddlist'>
<?php
    echo affiche_liste_jeux();
?>
</div>

<?php    

    require_once $config['paths']['template'] . "/footer.php";
?>