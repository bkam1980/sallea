<?php
require_once('inc/init.inc.php');
//------------------ TRAITEMENT ------------------------------

// Déconnexion demandée par l'internaute :
// on teste d'abord l'existence de l'indice 'action' avant de tester son contenu, sans quoi nous aurions une erreur dans le cas où il n'existerait pas :
if(isset($_GET['action']) && $_GET['action'] == 'deconnexion'){
	session_destroy();  // s'éxécute à la fin du script
}

// vérifier si internaute déjà connecté :
if (internauteEstConnecte()) {
	header('location:prof.php');  // envoie un entête au client qui demande la page profil.php
	exit();  // puis quitte ce script	
}

// Traitement du formulaire de connexion :
if(!empty($_POST)) {
	// on vérifie que pseudo et mdp correspondent en BDD :
	
	$_POST['mdp'] = md5($_POST['mdp']);  // pour comparer le mdp avec celui de la base, il faut le crypter lui aussi en md5
	$resultat = executeRequete("SELECT * FROM membre WHERE pseudo=:pseudo AND mdp= :mdp", array('pseudo'=>$_POST['pseudo'], 'mdp'=>$_POST['mdp']));
	
	var_dump($_POST['mdp']);
	
	if($resultat->rowCount() != 0) {  // si le nombre de lignes du jeu de résultats est différent de 0, c'est qu'il y a (au moins) un résultat pour lequel le pseudo correspond au mdp. On peut donc connecter le membre.
	
	$membre = $resultat->fetch(PDO::FETCH_ASSOC);  // pas de boucle while car il ne peut y avoir qu'un seul résultat dans notre requête
	
	// on remplit la session du membre avec ses infos :
	$_SESSION['membre'] = $membre;
	var_dump($_SESSION['membre']);
	
	// Direction vers la page profil :
	header('location:index.php'); 
	exit(); 
	} else {
		$contenu .='<div class="bg-danger">Erreur sur les identifiants</div>';
		
	}
}




//-------------------------- AFFICHAGE ------------------------
require_once('inc/haut.inc.php');
echo $contenu;
?>

<div class="container" style="min-height: 80vh;">
<h3>Veuillez renseigner votre pseudo et votre mot de passe pour vous connecter</h3>

<form method="post" action="">
	<label for="pseudo">Pseudo</label><br>
	<input type="text" id="pseudo" name="pseudo" required><br><br>
	
	<label for="mdp">Mot de passe</label><br>
	<input type="password" id="mdp" name="mdp" required><br><br>
	
	<input type="submit" value="se connecter" class="btn">
</form>
</div>















<?php
require_once('inc/bas.inc.php');