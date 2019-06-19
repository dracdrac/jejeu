<?php    
    $config = include('config.php');
    require_once $config['paths']['lib'] .  '/jeux.php';
    require_once $config['paths']['template'] . "/header.php";

?>
    <h2>Toutes les etiquettes</h2>

<?php    
    echo affiche_liste_etiquettes();

    require_once $config['paths']['template'] . "/footer.php";
?>