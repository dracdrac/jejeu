<?php

require_once('lib/Parsedown.php');
require_once('lib/utils.php');

// Retourne l'objet base de donnee
function connection()
{
    global $config;
    try
    {
        $bdd = new PDO('mysql:'
            .'host='.$config['db']['host'].';'
            .'dbname='.$config['db']['dbname'].';'
            .'charset=utf8',
            $config['db']['username'],
            $config['db']['password']);
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


function affiche_page($type, $id=NULL)
{
    global $config;
    ////// REQUETE BDD //////

    $table = 'jejeu_' . $type;

    if(!isset($id))
    {
        $req = connection()->prepare('SELECT *  FROM '.$table.' ORDER BY RAND() LIMIT 1');
        $req->execute(array($table));
    }
    else
    {
        $req = connection()->prepare('SELECT *  FROM '.$table.' WHERE id=?');
        $req->execute(array($id));
    }
    
    ////// FORMATAGE DES DONNEES //////

    if ($donnees = $req->fetch())
    {   
        // Parse la description longue (markdown)
        $Parsedown = new Parsedown();
        $donnees['description_longue'] = $Parsedown->text($donnees['description_longue']);
        // Ajouter liste si necessaire
        if ($type == 'jeux') {
            $donnees['liste_etiquettes'] = affiche_liste_etiquettes($id);
        }
        elseif ($type == 'etiquettes') {
            $donnees['liste_jeux'] = affiche_liste_jeux($id);
        }
        // Formate et affiche la page
        $template = 'page_' . substr($type, 0, -1); //enlève le pluriel  :'(
        $req->closeCursor(); 
        return formatString($config['templates'][$template], $donnees);
    }
    else
    {
        // affiche une erreur
        $req->closeCursor(); 
        return $config['templates']['inexistant'];
    }

}
// Affiche une page de jeu
function affiche_page_jeu($id=NULL)
{
    return affiche_page('jeux', $id);
}
// Affiche une page d'etiquette
function affiche_page_etiquette($id)
{
    return affiche_page('etiquettes', $id);
}
// Affiche une page d'article
function affiche_page_article($id)
{
    return affiche_page('articles', $id);
}


// Affiche la liste
function affiche_liste($type, $rel_id=NULL, $is_checkable=false, $is_administrable=false)
{
    global $config;

    ////// REQUETE BDD //////

    // table des elements de la liste
    $table = 'jejeu_' . $type;

    // S'il y a une contrainte de relation
    if (isset($rel_id))
    {
        // Type et table des elements à mettre en relation
        $rel_type = ($type == 'jeux') ? 'etiquettes' : 'jeux';
        $rel_table = 'jejeu_' . $rel_type;

        // Selectionne tous les elements de la table en relation avec rel_id
        $req = connection()->prepare('
            SELECT a.nom, a.id, a.description_courte
            FROM '.$table.' a, '.$rel_table.' b, jejeu_jeux_etiquettes c
            WHERE b.id = c.id_' . substr($rel_type, 0, -1)
            .' AND a.id = c.id_' . substr($type, 0, -1) 
            .' AND b.id = ?'
        );
        $req->execute(array($rel_id));

        // Si la liste est checkable, il faut aussi avoir une req négative
        if ($is_checkable)
        {
            // Selectionne tous les elements de la table PAS en relation avec rel_id
            $req_neg = $db->prepare('
                SELECT nom, id, description_courte
                FROM '.$table
                .'WHERE id NOT IN (SELECT a.id
                    FROM '.$table.' a, '.$rel_table.' b, jejeu_jeux_etiquettes c
                    WHERE b.id = c.id_' . substr($rel_type, 0, -1)
                    .' AND a.id = c.id_' . substr($type, 0, -1) 
                    .' AND b.id = ?)'
            );
            $req_neg->execute(array($rel_id));
        }
    }
    // Sinon
    else
    {
        // Selectionne tous les elements de la table
        $req = connection()->prepare('
            SELECT nom, id, description_courte
            FROM ' . $table);
        $req->execute();
    }

    ////// FORMATAGE DES DONNEES //////

    function elements_formates($template, $req)
    {
        global $config;
        $elements = "";
        while ($donnees = $req->fetch())
        {   
            // Formate et ajoute l'element
            $lien = formatString($config['templates'][$template], $donnees);
            $elements .= formatString($config['templates']['li'], ['element' => $lien]);
        }
        $req->closeCursor(); 
        return $elements;
    }


    if($is_checkable)
    {
        if(isset($req_neg))
        {
            $elements = elements_formates('checked_'.substr($type,0,-1), $req_neg)
                      . elements_formates('check_'.substr($type,0,-1), $req);
        }
        else
        {
            $elements = elements_formates('check_'.substr($type,0,-1), $req);
        }
    }
    elseif ($is_administrable)
    {
        # code...
    }
    else
    {
        $elements = elements_formates('lien_'.substr($type, 0, -1), $req);
    }

    // Formate et affiche la liste
    return formatString($config['templates']['ul'],['elements' => $elements]);

}


// Affiche la liste de tous les jeux
function affiche_liste_jeux($etiquette_id=NULL)
{
    return affiche_liste('jeux', $etiquette_id);
}
// Affiche la liste des etiquettes
function affiche_liste_etiquettes($jeu_id=NULL)
{
    return affiche_liste('etiquettes', $jeu_id);
}
// Affiche la liste des articles
function affiche_liste_articles()
{
    return affiche_liste('articles');
}
// Affiche la liste  check de tous les jeux
function affiche_check_jeux($etiquette_id=NULL)
{
    return affiche_liste('jeux', $etiquette_id, true);
}
// Affiche la liste check des etiquettes
function affiche_check_etiquettes($jeu_id=NULL)
{
    return affiche_liste('etiquettes', $jeu_id, true);
}


//////////////////////////////////////////////:

// Affiche un formulaire de etiquette
function affiche_etiquette_formulaire($id=NULL)
{
    global $config;
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

    $donnees['nom'] = htmlspecialchars($donnees['nom'], ENT_QUOTES, 'UTF-8');
    $donnees['description_courte'] = htmlspecialchars($donnees['description_courte'], ENT_QUOTES, 'UTF-8');


    $donnees['check_jeux'] = affiche_check_jeux($id);
    $donnees['id'] = $id;


    return formatString($config['templates']['etiquette_formulaire'], $donnees);
}


// Affiche un formulaire de jeux
function affiche_jeu_formulaire($id=NULL)
{
    global $config;
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

    $donnees['nom'] = htmlspecialchars($donnees['nom'], ENT_QUOTES, 'UTF-8');
    $donnees['description_courte'] = htmlspecialchars($donnees['description_courte'], ENT_QUOTES, 'UTF-8');

    $donnees['check_etiquettes'] = affiche_check_etiquettes($id);
    $donnees['id'] = $id;

    return formatString($config['templates']['jeu_formulaire'], $donnees);
}


// Affiche un formulaire d'etiquette
function affiche_article_formulaire($id=NULL)
{
    global $config;
    if (isset($id)){
        $req = connection()->prepare('
            SELECT id, nom, description_courte, description_longue
            FROM jejeu_articles
            WHERE id=?
            ');
        $req->execute(array($id));
        $donnees = $req->fetch();
        $req->closeCursor(); 
    }
    else{
        $donnees = $config['templates']['sample_article'];
    }

    $donnees['nom'] = htmlspecialchars($donnees['nom'], ENT_QUOTES, 'UTF-8');
    $donnees['description_courte'] = htmlspecialchars($donnees['description_courte'], ENT_QUOTES, 'UTF-8');

    $donnees['id'] = $id;

    return formatString($config['templates']['article_formulaire'], $donnees);
}


//
function affiche_liste_modifier_suprimer()
{
    global $config;
    $db = connection();

    // ETIQUETTES
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

    // JEUX
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

    // ARTICLES
    $req = $db->prepare('
    SELECT id, nom, description_courte
    FROM jejeu_articles
    ');
    $req->execute();

    $articles = "";
    while ($donnees = $req->fetch())
    {   
        // Formate et ajoute l'element
        $tplt = $config['templates']['lien_article'];
        $tplt .= ' <a href=admin.php?action=suprimerarticle&id={{id}}&submit>SUPRIMER</a> ';
        $tplt .= ' <a href=admin.php?action=modifierarticle&id={{id}}>MODIFIER</a> ';
        $lien = formatString($tplt, $donnees);
        $jeux .= formatString($config['templates']['li'], ['element' => $lien]);
    }
    $req->closeCursor(); 

    // Formate et affiche la liste

    $listes = 'jeux:' . formatString($config['templates']['ul'],['elements' => $jeux]) ;
    $listes .= 'etiquettes:'. formatString($config['templates']['ul'],['elements' => $etiquettes]);
    $listes .= 'articles:'. formatString($config['templates']['ul'],['elements' => $articles]);
    return $listes;
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

        // SUPRIMER /////////////
        // jeu
        if($action == 'suprimerjeu'){
            $req = $db->prepare('
                DELETE FROM  jejeu_jeux
                WHERE id=?
                ');
            $req->execute(array($_GET['id']));
            echo "Bye bye le jeu numéro " . $_GET['id'];
        }
        // etiquette
        elseif($action == 'suprimeretiquette'){
            $req = $db->prepare('
                DELETE FROM  jejeu_etiquettes
                WHERE id=?
                ');
            $req->execute(array($_GET['id']));
            echo "Bye bye l'etiquette numéro " . $_GET['id'];

        }
        // article
        elseif($action == 'suprimerarticle'){
            $req = $db->prepare('
                DELETE FROM  jejeu_articles
                WHERE id=?
                ');
            $req->execute(array($_GET['id']));
            echo "Bye bye l'article numéro " . $_GET['id'];

        }
        // MODIFIER /////////////
        // jeu
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
        // etiquette
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
        // article
        elseif($action == 'nouvelarticle'){
            // modifier
            $req = $db->prepare('
                UPDATE  jejeu_articles
                SET description_longue = :description_longue, nom = :nom, description_courte= :description_courte
                WHERE id=:id
                ');
            $req->execute(array(
                'id' => $_GET['id'],
                'nom' => $p['nom'],
                'description_courte' => $p['description_courte'],
                'description_longue' => $p['description_longue']
                ));
        }
 

    }
    // NOUVEAU //////////////////////////

    //jeu
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
    //etiquette
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
    //etiquette
    elseif ($action == 'nouvelarticle') {
        $req =$db->prepare('
            INSERT INTO jejeu_articles(nom, description_courte, description_longue)
            VALUES(:nom, :description_courte, :description_longue)');
        $req->execute(array(
            'nom' => $p['nom'],
            'description_courte' => $p['description_courte'],
            'description_longue' => $p['description_longue']
        ));
        echo 'Je pense que ça devrai être bon !';
    }
    else
    {
        echo 'Oups ! une erreur de type chelou...';
    }
}

