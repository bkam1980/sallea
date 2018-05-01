<?php
require_once('inc/init.inc.php');
//------------------ TRAITEMENT ------------------------------

//-------------------------- AFFICHAGE ------------------------
require_once('inc/haut.inc.php');
echo $contenu;
?>

<div class="container" style="min-height: 80vh;">
<h3>Contactez-nous</h3>

<form method="post" action="mailto:t.morel67@free.fr">
	<label for="nom">Nom</label><br>
	<input type="text" id="nom" name="nom" required><br><br>
	
	<label for="prenom">Prénom</label><br>
	<input type="text" id="prenom" name="prenom" required><br><br>
	
	<label for="email">Email</label><br>
	<input type="text" id="email" name="email" placeholder="exemple@gmail.com"required><br><br>
	
	<label for="message">Message :</label><br><textarea id="message" name="message" tabindex="4" cols="50" rows="8"></textarea>
	<br>
	<input type="submit" value="envoyer" class="btn">
</form>
</div>


<?php
require_once('inc/bas.inc.php');