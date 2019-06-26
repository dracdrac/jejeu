<?php    
    $config = include('config.php');
    require_once $config['paths']['lib'] .  '/jeux.php';
    require_once $config['paths']['template'] . "/header.php";

?>
    <h2>Toutes les etiquettes</h2>

    <div class='bddlist'>
<?php
    echo affiche_liste_etiquettes();
?>
</div>

<?php    

    require_once $config['paths']['template'] . "/footer.php";
?>