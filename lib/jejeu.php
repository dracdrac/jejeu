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
///////// AFFICHAGE ///////////////
///////////////////////////////////


function affiche_page($categorie, $id=NULL)
{
    global $config;

    // Le type correspont à la catégorie au singulier (e.g. jeux -> jeu)
    $type = substr($categorie, 0, -1);

    ////// REQUETE BDD //////

    $table = 'jejeu_' . $categorie;
    if(!isset($id))
    {
        $req = connection()->prepare('SELECT *  FROM '.$table.' WHERE nom NOT LIKE "\_%" ORDER BY RAND() LIMIT 1');
    }
    else
    {
        $req = connection()->prepare('SELECT *  FROM '.$table.' WHERE id=?');
    }
    $req->execute(array($id));
    
    ////// FORMATAGE DES DONNEES //////

    if ($donnees = $req->fetch())
    {   
        // Parse la description longue (markdown)
        $Parsedown = new Parsedown();
        $donnees['description_longue'] = $Parsedown->text($donnees['description_longue']);
        // Ajouter liste si necessaire
        if ($categorie == 'jeux')
        {
            $donnees['liste_etiquettes'] = affiche_liste_etiquettes($donnees['id']);
        }
        elseif ($categorie == 'etiquettes')
        {
            $donnees['liste_jeux'] = affiche_liste_jeux($donnees['id']);
        }
        // Formate et affiche la page
        $template = 'page_' . $type; 
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
function affiche_liste($categorie, $liste_type, $rel_id=NULL)
{
    global $config;

    // Le type correspont à la catégorie au singulier (e.g. jeux -> jeu)
    $type = substr($categorie, 0, -1);

    ////// REQUETE BDD //////

    // table des elements de la liste
    $table = 'jejeu_' . $categorie;

    // S'il y a une contrainte de relation
    if (isset($rel_id))
    {
        // categorie et table des elements à mettre en relation
        $rel_categorie = ($categorie == 'jeux') ? 'etiquettes' : 'jeux';
        $rel_type = substr($rel_categorie, 0, -1);
        $rel_table = 'jejeu_' . $rel_categorie;

        // Selectionne tous les elements de la table en relation avec rel_id
        $req = connection()->prepare('
            SELECT a.nom, a.id, a.description_courte, TRUE AS related 
            FROM '.$table.' a, '.$rel_table.' b, jejeu_jeux_etiquettes c
            WHERE b.id = c.id_' . substr($rel_categorie, 0, -1)
            .' AND a.id = c.id_' . substr($categorie, 0, -1) 
            .' AND b.id = ?'
        );
        $req->execute(array($rel_id));

        // Si la liste est checkable, il faut aussi inclure les negatif
        if ($liste_type == 'check')
        {
            $req = connection()->prepare('
                (SELECT a.nom, a.id, a.description_courte, TRUE AS related 
                FROM '.$table.' a, '.$rel_table.' b, jejeu_jeux_etiquettes c
                WHERE b.id = c.id_' . substr($rel_categorie, 0, -1)
                    .' AND a.id = c.id_' . substr($categorie, 0, -1) 
                    .' AND b.id = ?)
                UNION
                (SELECT nom, id, description_courte, FALSE AS related 
                FROM '.$table
                .' WHERE id NOT IN (SELECT a.id
                    FROM '.$table.' a, '.$rel_table.' b, jejeu_jeux_etiquettes c
                    WHERE b.id = c.id_' . $rel_type
                    .' AND a.id = c.id_' . $type
                    .' AND b.id = ?))'
            );
            $req->execute(array($rel_id, $rel_id));
        }
    }
    // Sinon
    else
    {
        // Selectionne tous les elements de la table
        $req = connection()->prepare('
            SELECT nom, id, description_courte, FALSE AS related
            FROM ' . $table);
        $req->execute();
    }

    ////// FORMATAGE DES DONNEES //////

    $elements = "";
    while ($donnees = $req->fetch())
    {   
        // ne traite pas les objets qui commencent par '_' dans les listes de lien
        if($liste_type == 'lien' and substr($donnees["nom"], 0, 1) == '_'){
            continue;
        }
        $donnees["type"] = $type;
        $donnees["categorie"] = $categorie;
        $donnees["check_attribute"] = $donnees["related"] ? 'checked' : '';

        // Formate et ajoute l'element
        $lien = formatString($config['templates'][$liste_type], $donnees);
        $elements .= formatString($config['templates']['li'], ['element' => $lien]);
    }
    $req->closeCursor(); 
    return $elements;

 
    // Formate et affiche la liste
    return formatString($config['templates']['ul'],['elements' => $elements]);

}


// Affiche la liste de tous les jeux
function affiche_liste_jeux($etiquette_id=NULL)
{
    return affiche_liste('jeux', 'lien', $etiquette_id);
}
// Affiche la liste des etiquettes
function affiche_liste_etiquettes($jeu_id=NULL)
{
    return affiche_liste('etiquettes', 'lien', $jeu_id);
}
// Affiche la liste des articles
function affiche_liste_articles()
{
    return affiche_liste('articles', 'lien');
}
// Affiche la liste  check de tous les jeux
function affiche_check_jeux($etiquette_id=NULL)
{
    return affiche_liste('jeux', 'check', $etiquette_id);
}
// Affiche la liste check des etiquettes
function affiche_check_etiquettes($jeu_id=NULL)
{
    return affiche_liste('etiquettes', 'check', $jeu_id);
}

// Affiche les listes administrables de tous les elements
function affiche_listes_administrables()
{
    global $config;
    return formatString($config['templates']['listes_administrables'],[
        'liste_jeux' => affiche_liste('jeux', 'administrable'),
        'liste_etiquettes' => affiche_liste('etiquettes', 'administrable'),
        'liste_articles' => affiche_liste('articles', 'administrable'),
    ]);
}


//////////////////////////////////////////////:

function affiche_formulaire($categorie, $id=NULL)
{
    global $config;

    // Le type correspont à la catégorie au singulier (e.g. jeux -> jeu)
    $type = substr($categorie, 0, -1);

    ////// REQUETE BDD //////

    $table = 'jejeu_' . $categorie;
    if(!isset($id))
    {
        // nouvel element
        $donnees = $config['templates']['sample_' . $type];
    }
    else
    {
        // modifier element
        $req = connection()->prepare('SELECT *  FROM '.$table.' WHERE id=?');
        $req->execute(array($id));
        $donnees = $req->fetch();
        $req->closeCursor(); 
    }
    
    ////// FORMATAGE BDD //////

    $donnees['nom'] = htmlspecialchars($donnees['nom'], ENT_QUOTES, 'UTF-8');
    $donnees['description_courte'] = htmlspecialchars($donnees['description_courte'], ENT_QUOTES, 'UTF-8');

    if ($categorie == 'jeux')
    {
        $donnees['check_etiquettes'] = affiche_check_etiquettes($id);
    } elseif ($categorie == 'etiquettes')
    {
        $donnees['check_jeux'] = affiche_check_jeux($id);
    }
    
    $donnees['id'] = $id;

    return formatString($config['templates']['formulaire_' . $type], $donnees);
}



///////////////////////////////////
///////// SUBMIT //////////////////
///////////////////////////////////

// Soumet un formulaire (admin)
function submit($action, $categorie, $p)
{

    global $config;
    $db = connection();
    $type = substr($categorie, 0, -1);
    $table = 'jejeu_' . $categorie;
    if (isset($_GET['id']) && is_numeric($_GET['id']))
    {

        // SUPRIMER /////////////
        if($action == 'suprimer')
        {
            $req = $db->prepare('
                DELETE FROM ' . $table
                . ' WHERE id=?
                ');
            $req->execute(array($_GET['id']));
            echo "Bye bye " . $type . " numéro " . $_GET['id'];
        }
        // MODIFIER /////////////
        elseif($action == 'update')
        {
            // modifier
            $req = $db->prepare('
                UPDATE  '.$table
                . ' SET description_longue = :description_longue, nom = :nom, description_courte = :description_courte
                WHERE id=:id
                ');
            $req->execute(array(
                'id' => $_GET['id'],
                'nom' => $p['nom'],
                'description_courte' => $p['description_courte'],
                'description_longue' => $p['description_longue']
            ));
            if ($categorie != 'articles')
            {
                $rel_categorie = ($categorie == 'jeux') ? 'etiquettes' : 'jeux';
                $rel_type = ($type == 'jeu') ? 'etiquette' : 'jeu';
                // suprimer etiquetttes/jeux relations
                $req = $db->prepare('
                    DELETE FROM  jejeu_jeux_etiquettes
                    WHERE id_'.$type.'=?
                ');
                $req->execute(array($_GET['id']));
                // recreer etiquetttes/jeux relations
                if(isset($p[$rel_categorie]))
                {
                    foreach ($p[$rel_categorie] as $rel_id)
                    {
                        $req =connection()->prepare('
                            INSERT INTO jejeu_jeux_etiquettes(id_etiquette, id_jeu)
                            VALUES(:id_etiquette, :id_jeu)');
                        $req->execute(array(
                            'id_' . $rel_type => $rel_id,
                            'id_' . $type => $_GET['id']));
                    }
                }
            }
            echo "J'ai l'impression que la modif a bien eu lieu..<br/>"
                . formatString($config['templates']['lien'], array(
                    "id" => $_GET['id'],
                    "type" => $type,
                    "nom" => $p['nom'],
                    "description_courte" => $p['description_courte'],
                )
            );
        }
    }
    // NOUVEAU //////////////////////////
    elseif ($action == 'update') {
        $req =$db->prepare('
            INSERT INTO '.$table.'(nom, description_courte, description_longue)
            VALUES(:nom, :description_courte, :description_longue)');
        $req->execute(array(
            'nom' => $p['nom'],
            'description_courte' => $p['description_courte'],
            'description_longue' => $p['description_longue']
        ));
        $id = $db->lastInsertId();
        if ($categorie != 'articles')
        {
            $rel_categorie = ($categorie == 'jeux') ? 'etiquettes' : 'jeux';
            $rel_type = ($type == 'jeu') ? 'etiquette' : 'jeu';
            // Creer relation etiquette/jeu
            if(isset($p[$rel_categorie]))
            {
                foreach ($p[$rel_categorie] as $rel_id)
                {
                    $req =connection()->prepare('
                        INSERT INTO jejeu_jeux_etiquettes(id_etiquette, id_jeu)
                        VALUES(:id_etiquette, :id_jeu)');
                    $req->execute(array(
                        'id_' . $rel_type => $rel_id,
                        'id_' . $type => $_GET['id']));
                }
            }

        }
        echo "Ça a l'air de s'être bien passé <br/>"
            . formatString($config['templates']['lien'], array(
                "id" => $_GET['id'],
                "type" => $type,
                "nom" => $p['nom'],
                "description_courte" => $p['description_courte'],
            )
        );
    }
    else
    {
        echo 'Oups ! une erreur de type chelou...';
    }
}