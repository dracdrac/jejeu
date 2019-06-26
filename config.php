<?php
 return array(
    "info" => array(
        "baseUrl" => "http://jejeu.org"
    ),
    "db" => include('dbconfig.php'),
    // dbconfig.php must return an array :
    //  array(
    //     "dbname" => ###,
    //     "username" =>  ###,
    //     "password" => ###,
    //     "host" => ###
    // ),
    "paths" => array(
        "lib" => "lib",
        "template" => "template",
        "resources" => "/path/to/resources",
        "images" => array(
            "content" => $_SERVER["DOCUMENT_ROOT"] . "/images/content",
            "layout" => $_SERVER["DOCUMENT_ROOT"] . "/images/layout"
        )
    ),
    "templates" => array(

        // PAGES

        "page_jeu" => "
            <a href='jeu.php?id={{id}}'><h2 class='jeu'>{{nom}}</h2></a>
            <p class='descriptioncourte'>{{description_courte}}</p>
            <div class='descriptionlongue'>
            {{description_longue}}
            </div>
            <p>Les etiquettes attachées à ce jeu :</p>
            {{liste_etiquettes}}
        ",

        "page_etiquette" => "
            <a href='etiquette.php?id={{id}}'><h2 class='etiquette'>{{nom}}</h2></a>
            <p class='descriptioncourte'>{{description_courte}}</p>
            <div class='descriptionlongue'>
            {{description_longue}}
            </div>
            <p>Les jeux auxquels cette etiquette est attachée :</p>
            {{liste_jeux}}
        ",

        "page_article" => "
            <h2 class='etiquette'>{{nom}}</h2>
            <p class='descriptioncourte'>{{description_courte}}</p>
            <div class='descriptionlongue'>
            {{description_longue}}
            </div>
        ",

        // LIST HELPERS

        "ul" => "<ul>{{elements}}</ul>",

        "li" => "<li>{{element}}</li>",

        // CHECKBOXES ET LIENS

        "checked_jeu" => "
        <input type='checkbox' name='jeux[]' value='{{id}}' checked/>
        <a href='jeu.php?id={{id}}' class='jeu'>{{nom}}</a>
        <span class='descriptioncourte'>{{description_courte}}</span>
        ",
        "check_jeu" => "
        <input type='checkbox' name='jeux[]' value='{{id}}'/>
        <a href='jeu.php?id={{id}}' class='jeu'>{{nom}}</a>
        <span class='descriptioncourte'>{{description_courte}}</span>
        ",

        "lien_jeu" => "
        <a href='jeu.php?id={{id}}' class='jeu'>{{nom}}</a>
        <span class='descriptioncourte'>{{description_courte}}</span>
        ",

        "check_etiquette" => "
        <input type='checkbox' name='etiquettes[]' value='{{id}}' />
        <a href='etiquette.php?id={{id}}' class='etiquette'>{{nom}}</a>
        <span class='descriptioncourte'>{{description_courte}}</span>
        ",

        "checked_etiquette" => "
        <input type='checkbox' name='etiquettes[]' value='{{id}}' checked />
        <a href='etiquette.php?id={{id}}' class='etiquette'>{{nom}}</a>
        <span class='descriptioncourte'>{{description_courte}}</span>
        ",

        "lien_etiquette" => "
        <a href='etiquette.php?id={{id}}' class='etiquette'>{{nom}}</a>
        <span class='descriptioncourte'>{{description_courte}}</span>
        ",

        "lien_article" => "
        <a href='article.php?id={{id}}' class='article'>{{nom}}</a>
        <span class='descriptioncourte'>{{description_courte}}</span>
        ",

        // ADMIN

        "jeu_formulaire" => "
        <h2>Creer/modifier jeu</h2> 
        <form action='admin.php?action=nouveaujeu&id={{id}}&submit' method='post'>
            <label>Nom:</label>
            <input type='text' name='nom' value='{{nom}}' size='50'>
            <label>Description courte: </label>
            <input type='text' name='description_courte' value='{{description_courte}}' size='100'>
            <label>Description longue: </label>
            <textarea name='description_longue' id='description_longue'>{{description_longue}}</textarea>
            <script type='text/javascript'>
                var editor = new Editor({element: document.getElementById('description_longue')});
                editor.render();
            </script>
            <label>Etiquettes :</label>
            {{check_etiquettes}}
            <input type='submit' value='Je valide !'>
        </form> 
        ",

        "sample_jeu" => array(
            'nom' => 'Le blob',
            'description_courte' => "Tout le monde veut blober des contre-blobs !",
            'description_longue' => "Un honneur de présenter ce jeu !\n\n# Specifications\n\n- nombre de joueurs : entre 3 et 5\n- materiel necessaire :\n    - un plateau de blob (voir ci dessous)\n    - deux dés à 6 faces\n    - de quoi écrire\n    - un pion par joueur\n- durée d'une partie : entre 5 et 10 minutes\n\n## Le plateau de blob\n\nPour construire un plateau de blob, prenez une feuille A4 dessinez trois ronds concentriques dedans et faites une grande croix qui traverse tous les cercles.\n\n# Règles de base\n\n## Le placement\n\nChacun son tour on place son pion où on veut sur le plateau.\n\n## La bagarre\n\nTout le monde se bat.\n\n## La fin\n\nCelui qui a gagné récupère autant de point-blob qu'il y a de joueur\n\n# Variantes\n\nMalgrè que ce jeu ne soit pas très connu il existe beaucoup de variantes. Voilà une petite selection :\n\n## Le contre-blob\n\nOn peut contre-blober un point blob en criant une certaine formule magique  décidée à l'avance\n\n## L'abdominal\n\nTout est dans le titre...\n\n# Stratégie\n\nLa meilleur stratégie est de faire un max de point en un moins de temps. Donc ne pas se faire blober et rester fier."
            ),

        "etiquette_formulaire" => "
        <h2>Creer/modifier etiquette</h2> 
        <form action='admin.php?action=nouvelleetiquette&id={{id}}&submit' method='post'>
            <label>Nom:</label>
            <input type='text' name='nom' value='{{nom}}' size='50'>
            <label>Description courte: </label>
            <input type='text' name='description_courte' value='{{description_courte}}' size='100'>
            <label>Description longue: </label>
            <textarea name='description_longue' id='description_longue'>{{description_longue}}</textarea>
            <script type='text/javascript'>
                var editor = new Editor({element: document.getElementById('description_longue')});
                editor.render();
            </script>
            <label>Jeux :</label>
            {{check_jeux}}
            <input type='submit' value='Je valide !'>
        </form> 
        ",

        "sample_etiquette" => array(
            'nom' => 'Méga cool',
            'description_courte' => 'Des jeux vraiment au top !',
            'description_longue' => "Il y a des jeux bofs et des jeux **vraiments super cool**. Cette etiquette est pour les jeux de la deuxième catégorie."
            ),

        "article_formulaire" => "
        <h2>Creer/modifier article</h2> 
        <form action='admin.php?action=nouvelarticle&id={{id}}&submit' method='post'>
            <label>Nom:</label>
            <input type='text' name='nom' value='{{nom}}' size='50'>
            <label>Description courte: </label>
            <input type='text' name='description_courte' value='{{description_courte}}' size='100'>
            <label>Description longue: </label>
            <textarea name='description_longue' id='description_longue'>{{description_longue}}</textarea>
            <script type='text/javascript'>
                var editor = new Editor({element: document.getElementById('description_longue')});
                editor.render();
            </script>
            <input type='submit' value='Je valide !'>
        </form> 
        ",

        "sample_article" => array(
            'nom' => 'Faut-il abandonner ?',
            'description_courte' => 'L\'abandon est-il fairplay ?',
            'description_longue' => "Alors je dirai à la fois oui et non. En fait ça dépend."
            ),
        // DIVERS

        "inexistant" => "
            <h2>Oups...</h2>
            <p>Cette page n'existe pas.</p>

        "
    )
);
?>