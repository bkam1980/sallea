<?php
require_once('../inc/init.inc.php');
//------------------ TRAITEMENT ------------------------------


// 1 - VERIFICATION ADMIN
if (!internauteEstConnecteEtEstAdmin()) {
	header('location:../connex.php');  
	exit();
}

// 7 - SUPPRESSION DE LA SALLE

if (isset($_GET['action']) && $_GET['action'] == 'suppression' && isset($_GET['id_salle'])){
	
	// on sélectionne en base la photo (url) pour pouvoir supprimer le fichier physique du serveur :
	$resultat = executeRequete("SELECT photo FROM salle WHERE id_salle = :id_salle", array('id_salle' => $_GET['id_salle']));
	
	$salle_a_supprimer = $resultat->fetch(PDO::FETCH_ASSOC); // pas de boucle while car on est certain de n'avoir qu'un seul résultat au plus
	
	//var_dump($salle_a_supprimer);
	
	$chemin_photo_a_supprimer = $_SERVER['DOCUMENT_ROOT'] . $salle_a_supprimer['photo']; // désigne le chemin absolu du fichier photo physique sur le serveur
	
	//var_dump($chemin_photo_a_supprimer);
	
	if (!empty($salle_a_supprimer['photo']) && file_exists($chemin_photo_a_supprimer)) {
		unlink($chemin_photo_a_supprimer);
	} // si le fichier à supprimer existe bien et que le chemin est bien dans la bdd (pour éviter de n'avoir qu'un dossier vide dans $chemin_photo_a_supprimer), alors on peut le supprimer

	executeRequete("DELETE FROM salle WHERE id_salle =:id_salle", array('id_salle'=>$_GET['id_salle']));
	$contenu .='<div class="bg-success">La salle a bien été supprimé.</div>';
	$_GET['action'] = 'affichage';  
}


// 2 - ONGLETS AFFICHAGE OU AJOUT (? vaut GET)
$contenu .= '<ul class=" nav nav-tabs">
				<li class=""><a href="?action=affichage">Affichage des salles</a></li> 
				<li class=""><a href="?action=ajout">Ajout d\'une salle</a></li>
			</ul>';
			
// 4- ENREGISTREMENT DE LA SALLE
if(!empty($_POST)){ // si le formulaire est soumis
	//var_dump($_POST);
	$photo_bdd ='';  // détermine l'url de la photo saisi en base, ici vide.
	
	// 9- Modification de la photo :
	
	if (isset($_POST['photo_actuelle'])) {
		$photo_bdd = $_POST['photo_actuelle'];
	}
	
	// a cet endroit, il faudrait faire tous les contrôles des champs du formulaire
	
	// 5 suite : PHOTO :
	//var_dump($_FILES);  // pour voir le contenu de la superglobale
	if (!empty($_FILES['photo']['name'])) {  // si une photo est uploadée.
	//L'indice 'photo' vient du name du formulaire, l'indice 'name' est prédéfini dans $_FILES
	$nom_photo = $_POST['titre'] . '-' .$_FILES['photo']['name'];  // désigne le nom du fichier photo unique
	
	$photo_bdd = RACINE_SITE . 'photo/' . $nom_photo; // désigne l'url absolue qui sera enregistrée en BDD correspondant à la photo
	//var_dump($photo_bdd); 
	
	$photo_dossier= $_SERVER['DOCUMENT_ROOT'] . $photo_bdd;  // désigne le chemin absolu complet pour sauvegarder le fichier photo physique sur le serveur
		//var_dump($photo_dossier);
		
		copy($_FILES['photo']['tmp_name'], $photo_dossier); // enregistre le fichier temporaire à l'adresse contenue $_FILES['photo']['tmp_name'] à l'endroit spécifié dans $photo_dossier
		
	}
	
	// 4- suite : enregistrement de la salle en base :
	executeRequete("REPLACE INTO salle (id_salle, titre, description, photo, pays, ville, adresse, cp, capacite, categorie) VALUES(:id_salle, :titre, :description, :photo, :pays, :ville, :adresse, :cp, :capacite, :categorie)",
	array('id_salle'=>$_POST['id_salle'],  
		  'titre'=>$_POST['titre'],
		  'description'=>$_POST['description'],
		  'photo'=>$photo_bdd,
		  'pays'=>$_POST['pays'],
		  'ville'=>$_POST['ville'],
		  'adresse'=>$_POST['adresse'],
		  'cp'=>$_POST['cp'],
		  'capacite'=>$_POST['capacite'],
		  'categorie'=>$_POST['categorie'],
		 
	));
	
	$contenu .= '<div class="bg-success">La salle a été enregistré</div>';
	$_GET['action'] ='affichage'; // on met 'affichage' dans $_GET['action'] pour déclencher l'affichage du tableau HTML des produits et ne plus afficher le formulaire 
	
} // fin du if(!empty($_POST))

// 6- AFFICHAGE DES SALLES DANS UNE TABLE HTML :

if((isset($_GET['action']) && $_GET['action'] == 'affichage') || !isset($_GET['action'])) { // si l'affichage est demandée Ou on arrive sur la page pour la première fois ($_GET['action'] n'existe pas)
	
$resultat = executeRequete("SELECT * FROM salle"); 
$contenu .= '<h3>Affichage des salles</h3>';
$contenu .= '<p>Nombre de salles : '. $resultat->rowCount() .'</p>';
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
	foreach($ligne as $indice => $information) {  // "parcourt $ligne par ses indices auxquels j'associe la valeur"
		if ($indice == 'photo') {
			$information = '<img src="'. $information . '" alt="" width="70" height="70" >';  // si on est sur le champ "photo", on ajoute une balise<img> autour de $information qui contient l'url de l'image. On peut aussi tout simplement ajouter les lignes et cellules "à la main"(sans foreach).
		}
	
		$contenu .='<td>'. $information .'</td>';  // $information contient les valeurs
		
	}
	// Ajoute les liens modifier et supprimer :
	$contenu .= '<td>
					<a href="?action=modification&id_salle='. $ligne['id_salle'] .'">modifier</a> -
					<a href="?action=suppression&id_salle='. $ligne['id_salle'] .'" onclick="return(confirm(\'Etes-vous certain de vouloir supprimer cette salle ?\'));">supprimer</a>	
				</td>';
				
				// dans les href, on concatène $ligne['id_produit'] pour avoir l'id du produit modifié ou supprimé dans $_GET. Ainsi, on peut cibler le DELETE ou le REPLACE sur cet id en particulier. Dans le onclick : la fonction confirm() retourne true (si l'internaute clique "ok") ou false (s'il clique "annuler"). Ainsi, "return true" ne bloque pas le lien <a>, alors que "return false" bloque le lien <a> tel que le ferait un e.preventDefault(). 
	
	$contenu .='</tr>';
	}

$contenu .= '</table>';
	
}


//-------------------------- AFFICHAGE ------------------------
require_once('../inc/haut.inc.php');
echo $contenu;

// 3- FORMULAIRE HTML :
// on affiche le formulaire si on est en ajout ou en modification :
if(isset($_GET['action']) && ($_GET['action'] == 'ajout' || $_GET['action'] == 'modification')) :  // on teste toujours l'existence de l'indice avant d'en vérifier le contenu. Condition avec ":" et "endif" à la fin du script

	// 8- Formulaire de modification avec présaisie des données : 
	if(isset($_GET['id_salle'])) {
		// si il y a un id_produit passé dans l'url, on sélectionne en BDD les infos de ce produit :
		$resultat = executeRequete("SELECT * FROM salle WHERE id_salle = :id_salle", array('id_salle' => $_GET['id_salle']));
		
		$salle_actuel = $resultat->fetch(PDO::FETCH_ASSOC); // pas de boucle while car il n'y a qu'un seul produit dans cette requête
		//var_dump($produit_actuel);
	}
	
?>
<div class="container" style="min-height: 80vh;">
<h3>Formulaire d'ajout ou de modification d'une salle</h3>
<form method="post" action="" enctype="multipart/form-data">

	<input type="hidden" id="id_salle" name="id_salle" value="<?php echo $salle_actuel['id_salle'] ?? 0; ?>"><!-- type "hidden" pour ne pas afficher le champ. Il contient l'id_produit qu'on utilisera en BDD. valeur 0 si on est en ajout, pour s'assurer que cet id n'existe pas en base, et donc utiliser un REPLACE INTO en tant que INSERT -->
	
	
	<label for="titre">Titre</label><br>
	<input type="text" id="reference" name="titre" value="<?php echo $salle_actuel['titre'] ?? ''; ?>"><br><br>
	
	<label for="description">Description</label><br>
	<textarea id="description" name="description" value="<?php echo $salle_actuel['description'] ?? ''; ?>"></textarea><br><br>
	
	<label for="pays">Pays</label><br>
	<input type="text" id="pays" name="pays" value="<?php echo $salle_actuel['pays'] ?? ''; ?>"><br><br>
	
	<label for="ville">Ville</label><br>
	<input type="text" id="ville" name="ville" value="<?php echo $salle_actuel['ville'] ?? ''; ?>"><br><br>
	
	<label for="adresse">Adresse</label><br>
	<input type="text" id="adresse" name="adresse" value="<?php echo $salle_actuel['adresse'] ?? ''; ?>"><br><br>
	
	<label for="cp">Code postal</label><br>
	<input type="text" id="cp" name="cp" value="<?php echo $salle_actuel['cp'] ?? ''; ?>"><br><br>
	
	
	<label for="capacite">Capacité</label><br>
	<input type="text" id="capacite" name="capacite" value="<?php echo $salle_actuel['capacite'] ?? ''; ?>"><br><br>
	
	<label for="categorie">Catégorie</label><br>
	<input type="text" id="categorie" name="categorie" value="<?php echo $salle_actuel['categorie'] ?? ''; ?>"><br><br>
	
	
	
	<label for="photo">Photo</label>
	<!-- 5- PHOTO -->
	<input type="file" id="photo" name="photo"><br><br><!-- le type "file" combiné à l'attribut enctype="multipart/form-data" permet d'uploader des fichiers, et de remplir la superglobale $_FILES -->
	<!-- 9-modification de la  photo :-->
	<?php
		if(isset($salle_actuel['photo'])) {
			echo '<p>Vous pouvez uploader une nouvelle photo.</p>';
			echo '<img src="'. $salle_actuel['photo'] .'" width="90" height="90" >';
			echo '<input type ="hidden" name="photo_actuelle" value="'. $salle_actuel['photo'].'">';  // cet input permet de remplir $_POST avec un indice "photo_actuelle" qui contient la valeur du champ photo de la BDD. Ainsi, si on ne charge pas une nouvelle photo, l'url actuel de l'ancienne photo est remis en base
		}
	
	?>

	<input type="submit" value="valider" class="btn">
</form>

</div>
<?php
endif;
require_once('../inc/bas.inc.php');

