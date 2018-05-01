<?php

require_once("../inc/init.inc.php");

// 1- Vérification si Admin :
if(!internauteEstConnecteEtEstAdmin())
{
	header("location:../connex.php");
	exit();
}

// 3- Suppression d'un membre :
if(isset($_GET['action']) && $_GET['action'] == "supprimer_membre" && isset($_GET['id_membre']))
{	// on ne peut pas supprimer son propre profil :
	if ($_SESSION['membre']['id_membre'] != $_GET['id_membre']) {
		executeRequete("DELETE FROM membre WHERE id_membre=:id_membre", array(':id_membre' => $_GET['id_membre']));
	} else {
		$contenu .= '<div class="bg-danger">Vous ne pouvez pas supprimer votre propre profil ! </div>';
	}
	
}

// 4- Modification statut membre :
if(isset($_GET['action']) && $_GET['action'] == "modifier_statut" && isset($_GET['statut']))
{
	$statut = ($_GET['statut'] == 0) ? 1 : 0;	// $statut prend la valeur 1 si la condition avant le "?" est vraie. Sinon, elle prend la valeur 0 (":" signifiant else)
	
	executeRequete("UPDATE membre SET statut = '$statut' WHERE id_membre=:id_membre", array(':id_membre' => $_GET['id_membre']));
} // remarque : les variables prennent des quotes en SQL ('$statut')


// 2- Préparation de l'affichage :
$resultat = executeRequete("SELECT * FROM membre");
$contenu .= '<h3> Membres inscrit </h3>';
$contenu .=  "Nombre de membre(s) : " . $resultat->rowCount();

$contenu .=  '<table class="table"> <tr>';
		// Affichage des entêtes :
		for($i = 0; $i < $resultat->columnCount(); $i++)
		{
			$colonne = $resultat->getColumnMeta($i);  // Retourne les métadonnées pour une colonne dans le jeu de résultats $resultat sous forme de tableau
			//var_dump($colonne);  // on y trouve l'indice "name"
			if ( $colonne['name'] != 'mdp') $contenu .= '<th>' . $colonne['name'] . '</th>';
		}
		
		$contenu .= '<th>Action</th>';
		$contenu .=  '</tr>';

		// Affichage des lignes :
		while ($membre = $resultat->fetch(PDO::FETCH_ASSOC))
		{
			$contenu .=  '<tr>';
			//var_dump($membre);
				foreach ($membre as $indice => $information)
				{
					
					if ($indice == 'date_enregistrement') {
		$information = date('d/m/Y H:i', strtotime($information));
	}
					
					
					if ($indice != 'mdp') $contenu .=  '<td>' . $information . '</td>';
				}
				$contenu .=  '<td>	
				<a href="?action=modifier_statut&id_membre=' . $membre['id_membre'] . '&statut='. $membre['statut'] .'"> modifier </a>-
				
				
			
				<a href="?action=supprimer_membre&id_membre=' . $membre['id_membre'] . '" onclick="return(confirm(\'Etes-vous sûr de vouloir supprimer ce membre?\'));"> supprimer </a>
				
				</td>';
			$contenu .=  '</tr>';
		}
$contenu .=  '</table>';


//-------------------------------------------------- Affichage ---------------------------------------------------------//
require_once("../inc/haut.inc.php");
echo $contenu;
require_once("../inc/bas.inc.php");


