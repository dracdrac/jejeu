<?php    
    $config = include('config.php');
    require_once $config['paths']['lib'] .  '/jeux.php';
    require_once $config['paths']['template'] . "/header.php";

?>
    <h2>JeJeu</h2>
    <p>Une super liste de super jeux !</p>
    <p>Ci-dessous, tous les jeux dans un ordre randomisé.</p>

<?php    
    echo affiche_liste_jeux();

    require_once $config['paths']['template'] . "/footer.php";
?>