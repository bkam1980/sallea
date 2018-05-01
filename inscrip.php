<?php
require_once('inc/init.inc.php');
$inscription = false;  // signifie inscription pas faite, donc on affiche le formulaire

//------------------------ TRAITEMENT DU FORMULAIRE --------------------
if (!empty($_POST)) {  // si le formulaire est posté
	// var_dump($_POST);
	
	// vérifier que tous les champs sont remplis :
	if (empty($_POST['pseudo']) || empty($_POST['mdp']) || empty($_POST['nom']) || empty($_POST['prenom']) || empty($_POST['email']) || empty($_POST['civilite'])) {
		$contenu='<div class="bg-danger">Veuillez remplir tous les champs !</div>';
	}
	
	// vérifier les caractères autorisés dans le pseudo :
	$verif_caractere = preg_match('#^[a-zA-Z0-9._-]+$#', $_POST['pseudo']);  // preg_match() renvoie 1 si le second string respecte l'expression régulière indiquée, sinon renvoie 0
	
	// Rappel de l'expression régulière :
	/* - elle est délimitée par des #
	   - ^ signifie commence par tout ce qui suit, alors que $ signifie finit par tout ce qui précède
	   - [] pour délimiter les intervalles (de a à z, de A à Z, de 0 à 9, auxquels on peut ajouter un '.', un '_' ou un '-')
	   - le + pour dire que les caractères sont acceptés de 0 à X fois
	*/
	
	// vérifier la longueur du pseudo en plus des caractères autorisés :
	if(strlen($_POST['pseudo']) < 4 || strlen($_POST['pseudo']) > 20 || !$verif_caractere){ // si$verif_caractere vaut 0 (="not true")
		$contenu .='<div class="bg-danger">Le pseudo doit contenir entre 4 et 20 caractères (lettres de A à Z et chiffres de 0 à 9)</div>';
	
	}
	
	
	// vérifier l'email :
	if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
		$contenu .='<div class="bg-danger">Email invalide !</div>';
		
	}
	
	// vérifier la valeur de la civilité :
	if($_POST['civilite'] != 'm' && $_POST['civilite'] !='f') {
		$contenu .='<div class="bg-danger">De quel genre êtes-vous ?</div>';
		
	}
	
	// si il n'y a pas d'erreur ($contenu est vide), on fait le traitement en base :
	if (empty($contenu)) {
		// vérifier l'unicité du pseudo :
		$membre = executeRequete("SELECT * FROM membre WHERE pseudo =:pseudo", array('pseudo'=>$_POST['pseudo'])); // cette fonction n'est pas poropre à PDO, nous l'avons déclarée dans fonction.inc.php. Elle attend 2 arguments : 1 requête SQL, et un array associatif qui permettra d'associer les marqueurs aux valeurs.
		
		// var_dump($membre);
		if($membre->rowCount() > 0) { // indique le nombre de lignes de résultats issus de la requête : si > 0, c'est que le pseudo est déjà pris
			$contenu .= '<div class="bg-danger">Pseudo indisponible. Veuillez en choisir un autre.</div>';
		} else {
			// encrypter le mot de passe :
			$_POST['mdp'] = md5($_POST['mdp']);  // la fonction md5() permet d'encrypter un string sur 32 caractères. Pour comparer un mdp avec un autre, il faut les passer tous les deux en md5().
			
			
			// Insertion en BDD :
			executeRequete(
			"INSERT INTO membre (pseudo, mdp, nom, prenom, email, civilite, date_enregistrement,  statut) VALUES(:pseudo, :mdp, :nom, :prenom, :email, :civilite, now(), 0)",
			array('pseudo'=>$_POST['pseudo'],
				  'mdp'=>$_POST['mdp'],
				  'nom'=>$_POST['nom'],
				  'prenom'=>$_POST['prenom'],
				  'email'=>$_POST['email'],
				  'civilite'=>$_POST['civilite'])
				 
		
			);
			
			$contenu .='<div class="bg-success">Vous êtes inscrit à notre site. <a href="connex.php"> Cliquez ici pour vous connecter.</a></div>';
			$inscription = true;			
		}	
	}	
}  // Fin du if(!empty($_POST))



//-------------------------- AFFICHAGE ------------------------
require_once('inc/haut.inc.php');
echo $contenu;  // pour afficher les messages destinés à l'internaute
// on affiche le formulaire que si l'inscription n'est pas faite :
if(!$inscription):  // -> endif (remplace accolades); attention, le endif est tout à la fin du script
?>
<!-- Contenu de la page -->
	<div class="container" style="min-height: 80vh;">
		<!-- ICI viendra le contenu spécifique à chaque page -->
<h3>Veuillez renseigner le formulaire pour vous inscrire</h3>

<form method="post" action="">
	<label for="pseudo">Pseudo</label><br>
	<input type="text" id="pseudo" name="pseudo" title="caractères acceptés : a-zA-Z0-9_." required value="<?php echo $_POST['pseudo'] ?? '';?>"><br><br>
	<!-- dans value, on fait un echo de $_POST['pseudo'] s'il existe, sinon echo d'un string vide pour ne rien afficher -->
	
	<label for="mdp">Mot de passe</label><br>
	<input type="password" id="mdp" name="mdp" required value="<?php echo $_POST['mdp'] ?? '';?>"><br><br>
	
	<label for="nom">Nom</label><br>
	<input type="text" id="nom" name="nom" value="<?php echo $_POST['nom'] ?? '';?>"><br><br>
	
	<label for="prenom">Prénom</label><br>
	<input type="text" id="prenom" name="prenom" value="<?php echo $_POST['prenom'] ?? '';?>"><br><br>
	
	<label for="email">Email</label><br>
	<input type="text" id="email" name="email" value="<?php echo $_POST['email'] ?? '';?>"><br><br>
	
	<label>Civilité</label><br>
	<input type="radio" value="m" name="civilite" checked>Homme
	<input type="radio" value="f" name="civilite" >Femme<br><br>
		
	<input type="submit" name="inscription" value="s'inscrire" class="btn">
	
	</form>
</div>

<?php
endif;
require_once('inc/bas.inc.php');