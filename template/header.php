<?php
    $config = include('config/config.php');
    require_once('lib/jejeu.php');

    require_once('counter/conn.php');
    require_once('counter/counter.php');
    //
    updateCounter($_SERVER['REQUEST_URI']);
    updateInfo();
?>

<!DOCTYPE html>
<html>
<head>
    <title>JeJeu</title>
    <meta charset="utf-8">
    <meta name="description" content="Une super liste de super jeux (et d'autres trucs)">
    <meta name="keywords" content="jeu, jeux, règles, règles de jeu, règles du jeu">
    <meta name="author" content="Louis Pezet et Léon Lenclos">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" type="text/css" href="css/normalize.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/webfonts/font.css">

    <link rel="stylesheet" href="http://lab.lepture.com/editor/editor.css" />
<script type="text/javascript" src="http://lab.lepture.com/editor/editor.js"></script>
<script type="text/javascript" src="http://lab.lepture.com/editor/marked.js"></script>

</head>
<body>
    <div id="header">

    <ul class="nav">
        <li><h1><a href="index.php">JeJeu</a></h1></li>
        <li><a href="apropos.php">À propos</a></li>
        <li><a href="random.php">Un jeu au hazard</a></li>
        <li><a href="jeux.php">Tous les jeux</a></li>
        <li><a href="etiquettes.php">Toutes les etiquettes</a></li>
    </ul>

    </div>
    <div id="content">
