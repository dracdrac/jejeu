<?php
    // PAGE : ADMINISTRATION
    include("lib/password_protect.php");
    require('template/header.php');
?>
    <h2>Administration du site </h2>
    <small>(Le genre d'endroit méga secret)</small>
    <ul class="nav">
      <li><a href="admin.php?action=update&categorie=jeux">Nouveau jeu</a></li>
      <li><a href="admin.php?action=update&categorie=etiquettes">Nouvelle etiquette</a></li>
      <li><a href="admin.php?action=update&categorie=articles">Nouvel article</a></li>
      <li><a href="admin.php?action=listes_administrables">Modifier / Suprimer</a></li>
      <li><a href="fileupload.php">Uploader des images</a></li>
      <li><a href="counter/view.php">Vues</a></li>
    </ul>

<?php

if (isset($_GET['action']) and in_array($_GET['action'], ['update', 'listes_administrables']))
{
  $action = $_GET['action'];
}
if (isset($_GET['categorie']) and in_array($_GET['categorie'], ['articles', 'jeux', 'etiquettes']))
{
  $categorie = $_GET['categorie'];
}
if (isset($_GET['id']) and is_numeric($_GET['id']))
{
  $id = $_GET['id'];
}
else {
  $id = NULL;
}


if (isset($action))
{
  if (isset($_GET['submit']))
  {
    submit($action, $categorie, $_POST);
  }
  else
  {
    if ($action == 'update')
    {
      echo affiche_formulaire($categorie, $id);
    }
    elseif ($action == 'listes_administrables')
    {
      echo affiche_listes_administrables();
    }
  }
}
require('template/footer.php');
?>