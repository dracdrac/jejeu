<?php
    // PAGE : ADMINISTRATION
    include("lib/password_protect.php");
    require('template/header.php');
?>
    <h2>Uploader des images <small>(endroit secret)</small></h2>

    <p> N.B: vu que cette page n'est accessible qu'aux administrateurs du site, je ne fais aucune vérification sur le fichier. À nous de vérifier si:<p>
      <ul>
        <li>c'est bien une image</li>
        <li>elle fait la bonne taille</li>
        <li>le nom ne contient ni espace ni carracteres speciaux</li>
        <li>le nom n'existe pas déjà dans la liste des images</li>
      </ul>


  <?php
  // Submit nouvelle image
  if(isset($_POST["submit"]))
  {
    echo "<h3>Resultat de l'upload </h3>";
      $target_dir = "images/";
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file))
    {
      echo "Le fichier ". basename( $_FILES["fileToUpload"]["name"]). " a bien été uploadé.";
    }
    else
    {
      echo "Une erreur s'est produite pendant que l'on importais l'image .........";
    }
  }
  ?>

<h3>Uploader une nouvelle image </h3>
  <form action="fileupload.php" method="post" enctype="multipart/form-data">
      Selectionner l'image:
      <input type="file" name="fileToUpload" id="fileToUpload">
      <input type="submit" value="Upload!!!!!" name="submit">
  </form>

<h3>Liste des images uplodées </h3>


<?php
$dir    = 'images/';
$images = array_diff(scandir($dir), [".", ".."]);
echo '<ul>';

foreach ($images as $key => $img) {

  echo '<li>';
  echo formatString($config['templates']['image_admin'], ['href'=>$dir.$img]);
  echo '</li>';
}

echo '</ul>';

?>


<?php

require('template/footer.php');
?>