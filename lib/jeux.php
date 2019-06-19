<?php

$config = include('config.php');
require_once $config['paths']['lib'] . '/Parsedown.php';
require_once $config['paths']['lib'] . '/utils.php';


function connection()
{
    // Retourne l'objet base de donnee
    $config = include('config.php');

    try
    {
        $bdd = new PDO('mysql:host='.$config['db']['host'].';dbname='.$config['db']['dbname'].';charset=utf8', $config['db']['username'], $config['db']['password']);
    }
    catch(Exception $e)
    {
        die('Erreur : '.$e->getMessage());
    }
    return $bdd;
}



///////////////////////////////////
///////////////////////////////////
///////// AFFICHAGE ///////////////
///////////////////////////////////


// Affiche une page de jeu
function affiche_page_jeu($id)
{
    $config = include('config.php');

    $req = connection()->prepare('SELECT *  FROM jejeu_jeux WHERE id=?');
    $req->execute(array($id));

    if ($donnees = $req->fetch())
    {   
        // Parse la description longue (markdown)
        $Parsedown = new Parsedown();
        $donnees['description_longue'] = $Parsedown->text($donnees['description_longue']);
        // Ajoute une donnée pour la liste des etiquettes
        $donnees['liste_etiquettes'] = affiche_liste_etiquettes($id);
        // Formate et affiche la page
        $req->closeCursor(); 
        return formatString($config['templates']['page_jeu'], $donnees);
    } else {
        // affiche une erreur
        $req->closeCursor(); 
        return $config['templates']['inexistant'];
    }

}

// Affiche une page d'etiquette
function affiche_page_etiquette($id)
{
    $config = include('config.php');

    $req = connection()->prepare('SELECT *  FROM jejeu_etiquettes WHERE id=?');
    $req->execute(array($id));

    if ($donnees = $req->fetch())
    {   
        // Parse la description longue (markdown)
        $Parsedown = new Parsedown();
        $donnees['description_longue'] = $Parsedown->text($donnees['description_longue']);
        // Ajoute une donnée pour la liste des jeux
        $donnees['liste_jeux'] = affiche_liste_jeux($id);
        // Formate et affiche la page
        $req->closeCursor(); 
        return formatString($config['templates']['page_etiquette'], $donnees);
    } else {
        // affiche une erreur
        $req->closeCursor(); 
        return $config['templates']['inexistant'];
    }

}


// Affiche la liste de tous les jeux
function affiche_liste_jeux($etiquette_id=NULL)
{
    $config = include('config.php');
    if (isset($etiquette_id)){
        $req = connection()->prepare('
            SELECT j.nom, j.id, j.description_courte
            FROM jejeu_jeux j, jejeu_etiquettes e, jejeu_jeux_etiquettes je
            WHERE e.id = je.id_etiquette
            AND j.id = je.id_jeu
            AND e.id = ?
        ');
        $req->execute(array($etiquette_id));
    }
    else{
        $req = connection()->prepare('
            SELECT nom, id, description_courte
            FROM jejeu_jeux');
        $req->execute();
    }

    $elements = "";
    while ($donnees = $req->fetch())
    {   
        // Formate et ajoute l'element
        $lien = formatString($config['templates']['lien_jeu'], $donnees);
        $elements .= formatString($config['templates']['li'], ['element' => $lien]);
    }
    $req->closeCursor(); 

    // Formate et affiche la liste
    return formatString($config['templates']['ul'],['elements' => $elements]);

}

// Affiche la liste des etiquettes
function affiche_liste_etiquettes($jeu_id=NULL)
{
    $config = include('config.php');
    if (isset($jeu_id)){
        $req = connection()->prepare('
            SELECT e.nom, e.id, e.description_courte
            FROM jejeu_jeux j, jejeu_etiquettes e, jejeu_jeux_etiquettes je
            WHERE e.id = je.id_etiquette
            AND j.id = je.id_jeu
            AND j.id = ?
        ');
        $req->execute(array($jeu_id));

    }
    else{
        $req = connection()->prepare('
            SELECT nom, id, description_courte
            FROM jejeu_etiquettes');
        $req->execute();
    }

    $elements = "";
    while ($donnees = $req->fetch())
    {   
        // Formate et ajoute l'element
        $lien = formatString($config['templates']['lien_etiquette'], $donnees);
        $elements .= formatString($config['templates']['li'], ['element' => $lien]);
    }
    $req->closeCursor(); 

    // Formate et affiche la liste
    return formatString($config['templates']['ul'],['elements' => $elements]);

}


// Affiche la liste  check de tous les jeux
function affiche_check_jeux($etiquette_id=NULL)
{
    $config = include('config.php');
    $db = connection();
        $elements = "";

    if(isset($etiquette_id))
    {    $req = $db->prepare('
                SELECT j.nom, j.id, j.description_courte
                FROM jejeu_jeux j, jejeu_etiquettes e, jejeu_jeux_etiquettes je
                WHERE j.id = je.id_jeu
                AND e.id = je.id_etiquette
                AND e.id = ?
            ');
        $req->execute(array($etiquette_id));
        while ($donnees = $req->fetch())
        {   
            // Formate et ajoute l'element
            $lien = formatString($config['templates']['checked_jeu'], $donnees);
            $elements .= formatString($config['templates']['li'], ['element' => $lien]);
        }
        $req->closeCursor(); 
        $req = $db->prepare('
                SELECT nom, id, description_courte
                FROM jejeu_jeux
                WHERE id NOT IN (SELECT j.id
                    FROM jejeu_jeux j, jejeu_etiquettes e, jejeu_jeux_etiquettes je
                    WHERE j.id = je.id_jeu
                    AND e.id = je.id_etiquette
                    AND e.id = ?)
            ');
        $req->execute(array($etiquette_id));
        while ($donnees = $req->fetch())
        {   
            // Formate et ajoute l'element
            $lien = formatString($config['templates']['check_jeu'], $donnees);
            $elements .= formatString($config['templates']['li'], ['element' => $lien]);
        }
        $req->closeCursor(); 

    }
    else {
        $req = $db->prepare('SELECT nom, id, description_courte FROM jejeu_jeux');
        $req->execute();

        while ($donnees = $req->fetch())
        {   
            // Formate et ajoute l'element
            $lien = formatString($config['templates']['check_jeu'], $donnees);
            $elements .= formatString($config['templates']['li'], ['element' => $lien]);
        }
        $req->closeCursor(); 
    }



    // Formate et affiche la liste
    return formatString($config['templates']['ul'],['elements' => $elements]);

}
// Affiche la liste check des etiquettes
function affiche_check_etiquettes($jeu_id=NULL)
{
    $config = include('config.php');
    $db = connection();
        $elements = "";

    if(isset($jeu_id))
    {    $req = $db->prepare('
                SELECT e.nom, e.id, e.description_courte
                FROM jejeu_jeux j, jejeu_etiquettes e, jejeu_jeux_etiquettes je
                WHERE e.id = je.id_etiquette
                AND j.id = je.id_jeu
                AND j.id = ?

            ');
        $req->execute(array($jeu_id));
        while ($donnees = $req->fetch())
        {   
            // Formate et ajoute l'element
            $lien = formatString($config['templates']['checked_etiquette'], $donnees);
            $elements .= formatString($config['templates']['li'], ['element' => $lien]);
        }
        $req->closeCursor(); 
        $req = $db->prepare('
                SELECT nom, id, description_courte
                FROM jejeu_etiquettes
                WHERE id NOT IN (SELECT e.id
                    FROM jejeu_jeux j, jejeu_etiquettes e, jejeu_jeux_etiquettes je
                    WHERE e.id = je.id_etiquette
                    AND j.id = je.id_jeu
                    AND j.id = ?)
            ');
        $req->execute(array($jeu_id));
        while ($donnees = $req->fetch())
        {   
            // Formate et ajoute l'element
            $lien = formatString($config['templates']['check_etiquette'], $donnees);
            $elements .= formatString($config['templates']['li'], ['element' => $lien]);
        }
        $req->closeCursor(); 

    }
    else {
        $req = $db->prepare('SELECT nom, id, description_courte FROM jejeu_etiquettes');
        $req->execute();

        while ($donnees = $req->fetch())
        {   
            // Formate et ajoute l'element
            $lien = formatString($config['templates']['check_etiquette'], $donnees);
            $elements .= formatString($config['templates']['li'], ['element' => $lien]);
        }
        $req->closeCursor(); 
    }



    // Formate et affiche la liste
    return formatString($config['templates']['ul'],['elements' => $elements]);
}


// Affiche un formulaire de etiquette
function affiche_etiquette_formulaire($id=NULL)
{
    $config = include('config.php');
    if (isset($id)){
        $req = connection()->prepare('
            SELECT id, nom, description_courte, description_longue
            FROM jejeu_etiquettes
            WHERE id=?
            ');
        $req->execute(array($id));
        $donnees = $req->fetch();
        $req->closeCursor(); 
    }
    else{
        $donnees = $config['templates']['sample_etiquette'];
    }


    $donnees['check_jeux'] = affiche_check_jeux($id);
    $donnees['id'] = $id;


    return formatString($config['templates']['etiquette_formulaire'], $donnees);
}


// Affiche un formulaire de jeux
function affiche_jeu_formulaire($id=NULL)
{
    $config = include('config.php');
    if (isset($id)){
        $req = connection()->prepare('
            SELECT id, nom, description_courte, description_longue
            FROM jejeu_jeux
            WHERE id=?
            ');
        $req->execute(array($id));
        $donnees = $req->fetch();
        $req->closeCursor(); 
    }
    else{
        $donnees = $config['templates']['sample_jeu'];
    }

    $donnees['check_etiquettes'] = affiche_check_etiquettes($id);
    $donnees['id'] = $id;

    return formatString($config['templates']['jeu_formulaire'], $donnees);
}


//
function affiche_liste_modifier_suprimer()
{
    $config = include('config.php');
    $db = connection();
    $req = $db->prepare('
        SELECT id, nom, description_courte
        FROM jejeu_etiquettes
        ');
    $req->execute();

    $etiquettes = "";
    while ($donnees = $req->fetch())
    {   
        // Formate et ajoute l'element
        $tplt = $config['templates']['lien_etiquette'];
        $tplt .= ' <a href=admin.php?action=suprimeretiquette&id={{id}}&submit>SUPRIMER</a> ';
        $tplt .= ' <a href=admin.php?action=modifieretiquette&id={{id}}>MODIFIER</a> ';
        $lien = formatString($tplt, $donnees);
        $etiquettes .= formatString($config['templates']['li'], ['element' => $lien]);
    }
    $req->closeCursor(); 


    $req = $db->prepare('
    SELECT id, nom, description_courte
    FROM jejeu_jeux
    ');
    $req->execute();

    $jeux = "";
    while ($donnees = $req->fetch())
    {   
        // Formate et ajoute l'element
        $tplt = $config['templates']['lien_jeu'];
        $tplt .= ' <a href=admin.php?action=suprimerjeu&id={{id}}&submit>SUPRIMER</a> ';
        $tplt .= ' <a href=admin.php?action=modifierjeu&id={{id}}>MODIFIER</a> ';
        $lien = formatString($tplt, $donnees);
        $jeux .= formatString($config['templates']['li'], ['element' => $lien]);
    }
    $req->closeCursor(); 

    // Formate et affiche la liste

    return 'jeux:' . formatString($config['templates']['ul'],['elements' => $jeux]) .'etiquettes:'. formatString($config['templates']['ul'],['elements' => $etiquettes]);
}





///////////////////////////////////
///////////////////////////////////
///////// SUBMIT //////////////////
///////////////////////////////////

// Soumet un formulaire (admin)
function submit($action, $p)
{
    $db = connection();

    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        if($action == 'suprimerjeu'){
            $req = $db->prepare('
                DELETE FROM  jejeu_jeux
                WHERE id=?
                ');
            $req->execute(array($_GET['id']));
            echo "Bye bye le jeu numéro " . $_GET['id'];
        }
        elseif($action == 'suprimeretiquette'){
            $req = $db->prepare('
                DELETE FROM  jejeu_etiquettes
                WHERE id=?
                ');
            $req->execute(array($_GET['id']));
            echo "Bye bye l'etiquette numéro " . $_GET['id'];

        }
        elseif($action == 'nouveaujeu'){
            // modifier
            $req = $db->prepare('
                UPDATE  jejeu_jeux
                SET description_longue = :description_longue, nom = :nom, description_courte = :description_courte
                WHERE id=:id
                ');
            $req->execute(array(
                'id' => $_GET['id'],
                'nom' => $p['nom'],
                'description_courte' => $p['description_courte'],
                'description_longue' => $p['description_longue']
            ));
            // suprimer etiquetttes/jeux relations
            $req = $db->prepare('
                DELETE FROM  jejeu_jeux_etiquettes
                WHERE id_jeu=?
            ');
            $req->execute(array($_GET['id']));
            // recreer etiquetttes/jeux relations
            if(isset($p['etiquettes'])){
                foreach ($p['etiquettes'] as $id_etiquette) {
                $req =connection()->prepare('
                    INSERT INTO jejeu_jeux_etiquettes(id_etiquette, id_jeu)
                    VALUES(:id_etiquette, :id_jeu)');
                $req->execute(array(
                    'id_etiquette' => $id_etiquette,
                    'id_jeu' => $_GET['id']));
                }
            }
            echo "J'ai l'impression que la modif a bien eu lieu..";

        }
        elseif($action == 'nouvelleetiquette'){
            // modifier
            $req = $db->prepare('
                UPDATE  jejeu_etiquettes
                SET description_longue = :description_longue, nom = :nom, description_courte= :description_courte
                WHERE id=:id
                ');
            $req->execute(array(
                'id' => $_GET['id'],
                'nom' => $p['nom'],
                'description_courte' => $p['description_courte'],
                'description_longue' => $p['description_longue']
            ));
            // suprimer etiquetttes/jeux relations
            $req = $db->prepare('
                DELETE FROM  jejeu_jeux_etiquettes
                WHERE id_etiquette=?
            ');
            $req->execute(array($_GET['id']));
            // recreer etiquetttes/jeux relations
            if(isset($p['jeux'])){
            foreach ($p['jeux'] as $id_jeu) {
                $req =connection()->prepare('
                    INSERT INTO jejeu_jeux_etiquettes(id_etiquette, id_jeu)
                    VALUES(:id_etiquette, :id_jeu)');
                $req->execute(array(
                    'id_etiquette' => $_GET['id'],
                    'id_jeu' => $id_jeu));
            }
            echo "J'ai l'impression que la modif s'est bien passée..";

        }

        }
 

    }
    elseif ($action == 'nouveaujeu') {
        $req =$db->prepare('
            INSERT INTO jejeu_jeux(nom, description_courte, description_longue)
            VALUES(:nom, :description_courte, :description_longue)');
        $req->execute(array(
            'nom' => $p['nom'],
            'description_courte' => $p['description_courte'],
            'description_longue' => $p['description_longue']
        ));
        $id_jeu = $db->lastInsertId();
        if(isset($p['etiquettes'])){
            foreach ($p['etiquettes'] as $id_etiquette) {
            $req =connection()->prepare('
                INSERT INTO jejeu_jeux_etiquettes(id_etiquette, id_jeu)
                VALUES(:id_etiquette, :id_jeu)');
            $req->execute(array(
                'id_etiquette' => $id_etiquette,
                'id_jeu' => $id_jeu));
        }

        }
        echo "Ça a l'air de s'être bien passé";
    }
    elseif ($action == 'nouvelleetiquette') {
        $req =$db->prepare('
            INSERT INTO jejeu_etiquettes(nom, description_courte, description_longue)
            VALUES(:nom, :description_courte, :description_longue)');
        $req->execute(array(
            'nom' => $p['nom'],
            'description_courte' => $p['description_courte'],
            'description_longue' => $p['description_longue']
        ));
        $id_etiquette = $db->lastInsertId();
        if(isset($p['jeux'])){
            foreach ($p['jeux'] as $id_jeu) {
                $req =connection()->prepare('
                    INSERT INTO jejeu_jeux_etiquettes(id_etiquette, id_jeu)
                    VALUES(:id_etiquette, :id_jeu)');
                $req->execute(array(
                    'id_etiquette' => $id_etiquette,
                    'id_jeu' => $id_jeu));
            }
        }
        echo 'Je pense que ça devrai être bon !';
    }
    else
    {
        echo 'Oups ! une erreur de type chelou...';
    }
}

