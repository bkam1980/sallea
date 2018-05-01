<?php
require_once('inc/init.inc.php');
//------------------ TRAITEMENT ------------------------------

// vérifier si membre est non connecté, auquel cas il n'a pas accés à son profil, mais nous l'envoyons vers la page de connexion :
if(!internauteEstConnecte()) {
	header('location:connex.php'); 
	exit();
}


// Préparation du contenu du profil à afficher :
 //var_dump($_SESSION); // pour voir le contenu de la session avec toutes les infos sur le membre
$contenu .= '<div class="container" style="min-height: 80vh;">
			<h2>Bonjour '. $_SESSION['membre']['pseudo'] . '</h2>';

if ($_SESSION['membre']['statut'] == 1) {
	$contenu .= '<p>Vous êtes connecté en tant qu\'administrateur.</p>';
	
} else {
	$contenu .= '<p>Vous êtes connecté en tant que membre.</p>';
}

$contenu .= '<h3>Voici vos informations de profil</h3>';
	$contenu .= '<p>Votre email : '.$_SESSION['membre']['email'] .'</p>';
	


/* 
	- afficher les commandes du membre s'il en a, sinon mettre un message 'aucune commande en cours.' Pour ce suivi, affichez l'd de la commande, sa date et son état dans un <ul><li>.
*/

$id_membre = $_SESSION['membre']['id_membre']; // l'id du membre connecté
  
  // requete en BDD:
  $resultat = executeRequete("SELECT id_commande, id_membre, id_produit, DATE_FORMAT(date_enregistrement, '%d/%m/%Y') AS date_enregistrement FROM commande WHERE id_membre = :id_membre", array('id_membre'=>$id_membre));  
  
if ($resultat->rowCount() >0){	
	// si il y a des lignes dans $resultat :
	$contenu .= '<ul> Voici vos commandes en cours :';
	while($commande_a_afficher = $resultat->fetch(PDO::FETCH_ASSOC)){
		 $contenu .= '<li>Commande : '. $commande_a_afficher['id_commande'] . ' en date du '. $commande_a_afficher['date_enregistrement'] . '</li>';
	}
	$contenu .= '</ul>';
	
} else {
	// si il n'y a pas de ligne dans $resultat :
	$contenu .= '<p> aucune commande en cours.</p>';
}
$contenu .= '</div><hr>';


//--------------------AFFICHAGE
require_once('inc/haut.inc.php');
echo $contenu;

require_once('inc/bas.inc.php');
?>

