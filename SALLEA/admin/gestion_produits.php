<?php
require_once('../inc/init.inc.php');
//------------------ TRAITEMENT ------------------------------

// 1 - VERIFICATION ADMIN
if (!internauteEstConnecteEtEstAdmin()) {
	header('location:../connex.php');  // je remonte dans le dossier parent avec ../ puis descend vers le fichier connexion.php
	exit();
}


// 7 - SUPPRESSION DU PRODUIT
if (isset($_GET['action']) && $_GET['action'] == 'suppression' && isset($_GET['id_produit'])){
	
	// on sélectionne en base la photo (url) pour pouvoir supprimer le fichier physique du serveur :
	$resultat = executeRequete("SELECT id_produit FROM produit WHERE id_produit = :id_produit", array('id_produit' => $_GET['id_produit']));
	
	$produit_a_supprimer = $resultat->fetch(PDO::FETCH_ASSOC); // pas de boucle while car on est certain de n'avoir qu'un seul résultat au plus
	
	//var_dump($produit_a_supprimer);

	executeRequete("DELETE FROM produit WHERE id_produit =:id_produit", array('id_produit'=>$_GET['id_produit']));
	$contenu .='<div class="bg-success">Le produit a bien été supprimé.</div>';
	$_GET['action'] = 'affichage';  // pour pouvoir entrer dans la condition du point 6 ci-dessous qui affiche le tableau HTML des produits
}

// 2 - ONGLETS AFFICHAGE OU AJOUT (? vaut GET)
$contenu .= '<ul class=" nav nav-tabs">
				<li class=""><a href="?action=affichage">Affichage des produits</a></li> 
				<li class=""><a href="?action=ajout">Ajout d\'un produit</a></li>
			</ul>';
			
// 4- ENREGISTREMENT DU PRODUIT
if(!empty($_POST)){ // si le formulaire est soumis
	// var_dump($_POST);
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
	var_dump($photo_bdd); 
	
	$photo_dossier= $_SERVER['DOCUMENT_ROOT'] . $photo_bdd;  // désigne le chemin absolu complet pour sauvegarder le fichier photo physique sur le serveur
		var_dump($photo_dossier);
		
		copy($_FILES['photo']['tmp_name'], $photo_dossier); // enregistre le fichier temporaire à l'adresse contenue $_FILES['photo']['tmp_name'] à l'endroit spécifié dans $photo_dossier
		
	}
	
	// 4- suite : enregistrement du produit en base :

	// Conversion date arrivee pour BDD avant l'insert en BDD

        $dateA = str_replace('/', '-', $_POST['date_arrivee']);
        $Date_arrivee = date('Y-m-d H:i', strtotime($dateA));
        
        // Conversion date depart pour BDD
        
        $dateD = str_replace('/', '-', $_POST['date_depart']);
        $Date_depart = date('Y-m-d H:i', strtotime($dateD));


	executeRequete("REPLACE INTO produit (id_produit,id_salle, date_arrivee, date_depart, prix) VALUES(:id_produit, :id_salle, :date_arrivee, :date_depart, :prix)",
	array('id_produit'=>$_POST['id_produit'],  
		  'id_salle'=>$_POST['id_salle'],
		  'date_arrivee'=>$Date_arrivee,
		  'date_depart'=>$Date_depart,
		  'prix'=>$_POST['prix'],
	
	));
	
	$contenu .= '<div class="bg-success">Le produit a été enregistré</div>';
	$_GET['action'] ='affichage'; // on met 'affichage' dans $_GET['action'] pour déclencher l'affichage du tableau HTML des produits et ne plus afficher le formulaire 
}	
	
	 // fin du if(!empty($_POST))

// 6- AFFICHAGE DES PRODUITS DANS UNE TABLE HTML :
if((isset($_GET['action']) && $_GET['action'] == 'affichage') || !isset($_GET['action'])) { // si l'affichage est demandée Ou on arrive sur la page pour la première fois ($_GET['action'] n'existe pas)
	
$resultat = executeRequete("SELECT * FROM produit"); // sélectionne tous les produits
$contenu .= '<h3>Affichage des produits</h3>';
$contenu .= '<p>Nombre de produits : '. $resultat->rowCount() .'</p>';
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
	
	//var_dump($information);
	if ($indice == 'id_salle') {
		$titrephoto = executeRequete("SELECT id_salle, titre, photo FROM salle WHERE id_salle='$information.'") ;
	

	$titrephotoplus = $titrephoto->fetch(PDO::FETCH_ASSOC);
		
		foreach($titrephotoplus as $indice => $informationplus);
	//var_dump($titrephotoplus['titre']);
			$information = '' .$information .' - '. $titrephotoplus['titre'] . '<br><img src="'. $titrephotoplus['photo'] . '"  alt="" width="70" height="70" > ';  // si on est sur le champ "photo", on ajoute une balise<img> autour de $information qui contient l'url de l'image. On peut aussi tout simplement ajouter les lignes et cellules "à la main"(sans foreach).
		}
	if ($indice == 'date_arrivee') {
		$information = date('d/m/Y H:i', strtotime($information));
	}
	
		if ($indice == 'date_depart') {
		$information = date('d/m/Y H:i', strtotime($information));
	}
	

		$contenu .='<td>'. $information .'</td>';  // $information contient les valeurs
		
	}
	// Ajoute les liens modifier et supprimer :
	$contenu .= '<td>
					<a href="?action=modification&id_produit='. $ligne['id_produit'] .'">modifier</a> -
					<a href="?action=suppression&id_produit='. $ligne['id_produit'] .'" onclick="return(confirm(\'Etes-vous certain de vouloir supprimer ce produit ?\'));">supprimer</a>	
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
	if(isset($_GET['id_produit'])) {
		// si il y a un id_produit passé dans l'url, on sélectionne en BDD les infos de ce produit :
		$resultat = executeRequete("SELECT * FROM produit WHERE id_produit = :id_produit", array('id_produit' => $_GET['id_produit']));
		
		$produit_actuel = $resultat->fetch(PDO::FETCH_ASSOC); // pas de boucle while car il n'y a qu'un seul produit dans cette requête
	
	}

?>
<div class="container" style="min-height: 80vh;">
<h3>Formulaire d'ajout ou de modification d'un produit</h3>
<form method="post" action="" enctype="multipart/form-data">

	<input type="hidden" id="id_produit" name="id_produit" value="<?php echo $produit_actuel['id_produit'] ?? 0; ?>">
	
	<label for="titre">Salle</label><br>
	<select name='id_salle'>
	
	<?php
	// requete pour recuperer l 'id salle en fonction du titre 
	$sallePDOS = executeRequete("SELECT * FROM salle");
	while ($salle = $sallePDOS->fetch(PDO::FETCH_ASSOC)) {
		?><option  value="<?php echo $salle['id_salle'] ?>"><?php echo $salle['id_salle']. ' - ' . $salle['titre']. ' - ' .$salle['adresse'];?>
		</option><?php
		//var_dump($salle);
	}

	?>
	
	</select>
		
      <p class="lead">Période</p>

      <!-- Date d'arrivée -->
      <div class="form-group">
				<label for="date_arrivee">Date d\'arrivée :</label>
				<div class="input-group">
				 <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
				<input  class="form-control" type="text" style=" width:120px" name="date_arrivee" id="date_arrivee" placeholder="JJ/MM/AAAA" required="required"/>
				</div>
	  </div>

      <!-- Date de départ -->
      <div class="form-group">
				<label for="date_depart">Date de départ :</label>
				<div class="input-group">
				 <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
				<input  class="form-control" type="text" style=" width:120px" name="date_depart" id="date_depart" placeholder="JJ/MM/AAAA" required="required"/>
				</div>
	  </div>
			  
  
	<br>

	<label for="prix">Prix en €</label><br>
	<input type="text" id="prix" name="prix" value="<?php echo $produit_actuel['prix'] ?? 0; ?>" ><br><br>
	
	<label for="prix">Etat</label><br>
	<input type="text" id="etat" name="etat" value="<?php echo $produit_actuel['etat'] ?? "libre"; ?>"><br><br>
	
	<input type="submit" value="valider" class="btn">
</form>


</div>

<?php
endif;
require_once('../inc/bas.inc.php');

