<?php

 return array(
    "info" => array(
        "baseUrl" => "http://jejeu.org"
    ),
    "db" => include('dbconfig.php'),
    // dbconfig.php must return an array :
    // array("dbname" => ###, "username" =>  ###, "password" => ###, "host" => ###)
    "templates" => array(

        // PAGES
        "page_jeu" => file_get_contents('template/jeu.html'),
        "page_etiquette" => file_get_contents('template/etiquette.html'),
        "page_article" => file_get_contents('template/article.html'),
        "listes_administrables" => file_get_contents('template/listes_administrables.html'),

        // LIST HELPERS
        "ul" => "<ul>{{elements}}</ul>",
        "li" => "<li>{{element}}</li>",

        // CHECKBOXES ET LIENS
        "lien" => file_get_contents('template/lien.html'),
        "check" => file_get_contents('template/check.html'),
        "administrable" => file_get_contents('template/administrable.html'),


        // ADMIN
        "formulaire_jeu" => file_get_contents("template/formulaire_jeu.html"),
        "formulaire_etiquette" => file_get_contents("template/formulaire_etiquette.html"),
        "formulaire_article" => file_get_contents("template/formulaire_article.html"),
        "sample_jeu" => array(
            'nom' => 'Le blob',
            'description_courte' => "Tout le monde veut blober des contre-blobs !",
            'description_longue' => file_get_contents('template/blob_description_longue.md')
        ),
        "sample_etiquette" => array(
            'nom' => 'Méga cool',
            'description_courte' => 'Des jeux vraiment au top !',
            'description_longue' => "Il y a des jeux bofs et des jeux **vraiments super cool**. Cette etiquette est pour les jeux de la deuxième catégorie."
        ),
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