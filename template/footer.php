</div>
<div id="footer">

    <ul class="nav">
        <li><a href="legal.php">Mentions légales</a></li>
        <li><a href="contact.php">Contact</a></li>
        <li><a href="admin.php">Admin</a></li>
        <?php
            if (isset($lien_modifier)) {
                ?>
        <li><a href="<?php echo $lien_modifier; ?>">Modifier</a></li>

                <?php
            }
        ?>
    </ul>
<!--             <p style="font-weight:bold;color:white;background:red;text-align: center;">/!\ ATTENTION /!\ TRAVAIL EN COURS. Très instable. Ne pas partager. bisou</p>
 -->
</div>
</body>
<!-- <script type="text/javascript" src="js/colors.js"></script>
 -->
</html>