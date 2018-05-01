<?php

require_once("../inc/init.inc.php");

//------------------ TRAITEMENT ------------------------------

// 1 - VERIFICATION ADMIN
if (!internauteEstConnecteEtEstAdmin()) {
	header('location:../connex.php');  // je remonte dans le dossier parent avec ../ puis descend vers le fichier connexion.php
	exit();
}


// 7 - SUPPRESSION D'UN AVIS
if (isset($_GET['action']) && $_GET['action'] == 'suppression' && isset($_GET['id_avis'])){
	
	// on sélectionne en base la photo (url) pour pouvoir supprimer le fichier physique du serveur :
	$resultat = executeRequete("SELECT id_avis FROM avis WHERE id_avis = :id_avis", array('id_avis' => $_GET['id_avis']));
	
	$avis_a_supprimer = $resultat->fetch(PDO::FETCH_ASSOC); // pas de boucle while car on est certain de n'avoir qu'un seul résultat au plus
	
	//var_dump($avis_a_supprimer);

	executeRequete("DELETE FROM avis WHERE id_avis =:id_avis", array('id_avis'=>$_GET['id_avis']));
	$contenu .='<div class="bg-success">L\'avis a bien été supprimé.</div>';
	$_GET['action'] = 'affichage';  // pour pouvoir entrer dans la condition du point 6 ci-dessous qui affiche le tableau HTML des avis
}



// 4- ENREGISTREMENT DE l'avis
if(!empty($_POST)){ // si le formulaire est soumis
	
	
	// 4- suite : enregistrement de l'avis en base :

	executeRequete("INSERT INTO avis (id_avis, id_membre, id_salle, commentaire, note,date_enregistrement) VALUES(:id_avis, :id_membre, :id_salle, :commentaire, :note, NOW() )",
	array('id_avis'=>$_POST['id_avis'],  
		  'id_membre'=>$_POST['id_membre'],
		  'id_salle'=>$_POST['id_salle'],
		  'commentaire'=>$_POST['commentaire'],
		  'note'=>$_POST['note']
		
		 	
	));
	
	$contenu .= '<div class="bg-success">L\'avis a été enregistré</div>';
	$_GET['action'] ='affichage'; // on met 'affichage' dans $_GET['action'] pour déclencher l'affichage du tableau HTML des avis et ne plus afficher le formulaire 
}	
	
	 // fin du if(!empty($_POST))

// 6- AFFICHAGE DES AVIS DANS UNE TABLE HTML :
if((isset($_GET['action']) && $_GET['action'] == 'affichage') || !isset($_GET['action'])) { // si l'affichage est demandée Ou on arrive sur la page pour la première fois ($_GET['action'] n'existe pas)
	
$resultat = executeRequete("SELECT * FROM avis"); // sélectionne tous les avis
$contenu .= '<h3>Affichage des avis</h3>';
$contenu .= '<p>Nombre d\'avis : '. $resultat->rowCount() .'</p>';
$contenu .= '<table class="table">';


	// affichage des entêtes :
	$contenu .='<tr>';
		for($i = 0; $i < $resultat->columnCount(); $i++) {
			$colonne = $resultat->getColumnMeta($i); // $resultat est un objet issu de la classe PDOStatement sur lequel on applique une méthode getColumnMeta($indice) qui retourne un array
			//var_dump($colonne);
			$contenu .= '<th>'. ucfirst($colonne['name']) .'</th>';  // c'est à l'indice 'name' de cet array que l'on trouve le nom du champ. ucfirst() pour mettre la première lettre en majuscule
		}		
			$contenu .= '<th>Action</th>';

	$contenu .='</tr>';
	
	// affichage des lignes :
	

	while($ligne = $resultat->fetch(PDO::FETCH_ASSOC)){
	$contenu .='<tr>';
	//var_dump($ligne);
	foreach($ligne as $indice => $information)   {  // "parcourt $ligne par ses indices auxquels j'associe la valeur"
	
	
		$contenu .='<td>'. $information .'</td>';  // $information contient les valeurs
		
	}
	// Ajoute les liens modifier et supprimer :
	$contenu .= '<td>
					<a href="?action=modification&id_avis='. $ligne['id_avis'] .'">modifier</a> -
					<a href="?action=suppression&id_avis='. $ligne['id_avis'] .'" onclick="return(confirm(\'Etes-vous certain de vouloir supprimer cet avis ?\'));">supprimer</a>	
				</td>';
				
				// dans les href, on concatène $ligne['id_avis'] pour avoir l'id du avis modifié ou supprimé dans $_GET. Ainsi, on peut cibler le DELETE ou le REPLACE sur cet id en particulier. Dans le onclick : la fonction confirm() retourne true (si l'internaute clique "ok") ou false (s'il clique "annuler"). Ainsi, "return true" ne bloque pas le lien <a>, alors que "return false" bloque le lien <a> tel que le ferait un e.preventDefault(). 
	
	$contenu .='</tr>';
	}

$contenu .= '</table>';
	
}



//-------------------------------------------------- Affichage ---------------------------------------------------------//
require_once("../inc/haut.inc.php");
echo $contenu;

require_once("../inc/bas.inc.php");

