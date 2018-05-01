<?php
require_once('inc/init.inc.php');
$inscription = false;  // signifie inscription pas faite, donc on affiche le formulaire

//------------------------ TRAITEMENT DU FORMULAIRE --------------------

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
	
	//var_dump($_POST);
	// 4- suite : enregistrement de l'avis en base :
	
	// Conversion date arrivee pour BDD avant l'insert en BDD

        $dateA = str_replace('/', '-', $_POST['date_enregistrement']);
        $Date_enregistrement = date('Y-m-d H:i', strtotime($dateA));
        

	executeRequete("INSERT INTO avis (id_avis, id_membre, id_salle, commentaire, note, date_enregistrement) VALUES(:id_avis, :id_membre, :id_salle, :commentaire, :note, :NOW())",
	array('id_avis'=>$_POST['id_avis'],  
		  'id_membre'=>$_POST['id_membre'],
		  'id_salle'=>$_POST['id_salle'],
		  'commentaire'=>$_POST['commentaire'],
		  'note'=>$_POST['note'],
		  'date_enregistrement'=>$Date_enregistrement)
		
		 	
	);
	
	$contenu .= '<div class="bg-success">L\'avis a été enregistré</div>';
	$_GET['action'] ='affichage'; // on met 'affichage' dans $_GET['action'] pour déclencher l'affichage du tableau HTML des avis et ne plus afficher le formulaire 
}	
	
	 // fin du if(!empty($_POST))

//-------------------------- AFFICHAGE ------------------------
require_once("inc/haut.inc.php");
echo $contenu;

?>
<div class="container" style="min-height: 80vh;">
<h3>Déposer un commentaire et une note</h3>
<form method="post" action="" enctype="multipart/form-data">

	<input type="hidden" id="id_avis" name="id_avis" value="<?php echo $avis_actuel['id_avis'] ?? 0; ?>"><!-- type "hidden" pour ne pas afficher le champ. Il contient l'id_avis qu'on utilisera en BDD. valeur 0 si on est en ajout, pour s'assurer que cet id n'existe pas en base, et donc utiliser un REPLACE INTO en tant que INSERT -->
	
	<input type="hidden" id="id_membre" name="id_membre" value="<?php echo $avis_actuel['id_membre'] ?? 0; ?>"><!-- type "hidden" pour ne pas afficher le champ. Il contient l'id_avis qu'on utilisera en BDD. valeur 0 si on est en ajout, pour s'assurer que cet id n'existe pas en base, et donc utiliser un REPLACE INTO en tant que INSERT -->
	
	
	<?php
	// requete pour recuperer l 'id membre
	$membrePDOS = executeRequete("SELECT * FROM membre");
	$membre = $membrePDOS->fetch(PDO::FETCH_ASSOC)
		?><h2><?php echo $_SESSION['membre']['pseudo']; ?></h2>
	
	<br><br>
	<label for="titre">Salle</label><br>
	<select name='id_salle'>
	<?php
	// requete pour recuperer l 'id salle en fonction du titre 
	$sallePDOS = executeRequete("SELECT * FROM salle");
	while ($salle = $sallePDOS->fetch(PDO::FETCH_ASSOC)) {
		?><option  value="<?php echo $salle['id_salle'] ?>"><?php echo $salle['titre'];?>
		</option><?php

	}
	
	?>
	
	</select><br><br>
	
	<label for="commentaire">Commentaire</label><br>
	<textarea id="commentaire" name="commentaire" ><?php echo $avis_actuel['commentaire'] ?? ''; ?></textarea><br><br>
	
	
	<label for="note">Note</label><br>
	<select name="note" class="form-group-sm form-control-static">
	<option value="1">1</option>
	<option value="2">2</option>
	<option value="3">3</option>
	<option value="4">4</option>
	<option value="5">5</option>
	</select>
	
	
	<input type="hidden" class="form-control" type="text" id="date_enregistrement" name="date_enregistrement" value="<?php echo $avis_actuel['date_enregistrement'] ?? 0; ?>">
	
	

	<input type="submit" value="valider" class="btn">
</form>


</div>

<?php

require_once("inc/bas.inc.php");

