<?php    
  include("password_protect.php");
    $config = include('config.php');
    require_once $config['paths']['template'] . "/header.php";
    require_once $config['paths']['lib'] . "/jeux.php";

?>
    <h2>Administration du site <small>(Le genre d'endroit méga secret)</small></h2>
    <ul class="nav">
      <li><a href="admin.php?action=nouveaujeu">Nouveau jeu</a></li>
      <li><a href="admin.php?action=nouvelleetiquette">Nouvelle etiquette</a></li>
      <li><a href="admin.php?action=nouvelarticle">Nouvel article</a></li>
      <li><a href="admin.php?action=modifier">Modifier / Suprimer</a></li>
    </ul>
<?php
  if (isset($_GET['action'])) {
    if (isset($_GET['submit'])) {
      submit($_GET['action'], $_POST);
    }else{
      if ($_GET['action'] == 'nouvelleetiquette') {
        echo affiche_etiquette_formulaire();
      }
      elseif ($_GET['action'] == 'nouveaujeu') {
        echo affiche_jeu_formulaire();
      }
      elseif ($_GET['action'] == 'nouvelarticle') {
        echo affiche_article_formulaire();
      }
      elseif ($_GET['action'] == 'modifier') {
        echo affiche_liste_modifier_suprimer();
      }
      elseif ($_GET['action'] == 'modifierjeu') {
        echo affiche_jeu_formulaire($_GET['id']);
      }
      elseif ($_GET['action'] == 'modifieretiquette') {
        echo affiche_etiquette_formulaire($_GET['id']);
      }
      elseif ($_GET['action'] == 'modifierarticle') {
        echo affiche_article_formulaire($_GET['id']);
      }
    }
  }


    require_once $config['paths']['template'] . "/footer.php";
?>