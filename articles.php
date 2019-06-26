<?php    
    $config = include('config.php');
    require_once $config['paths']['lib'] .  '/jeux.php';
    require_once $config['paths']['template'] . "/header.php";

?>
    <h2>Tous les articles</h2>

<?php    
    echo affiche_liste_articles();

    require_once $config['paths']['template'] . "/footer.php";
?>