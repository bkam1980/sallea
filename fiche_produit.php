<?php
require_once('inc/init.inc.php');
$aside =""; // pour l'affichage des salles suggérés

//-------------------------- TRAITEMENT------------------------
// 1- Contrôle de l'existence du produit demandé :
if (isset($_GET['id_produit'])) {
	$resultat = executeRequete("SELECT p.id_produit, DATE_FORMAT (p.date_arrivee, '%d/%m/%Y') AS date_arrivee, DATE_FORMAT(p.date_depart, '%d/%m/%Y') AS date_depart , p.prix, p.etat, p.id_salle, s.id_salle, s.titre, s.description, s.photo, s.ville, s.adresse, s.cp, s.capacite, s.categorie
		FROM produit p, salle s  WHERE s.id_salle = p.id_salle AND id_produit = :id_produit", array('id_produit' => $_GET['id_produit']));  // on sélectionne le produit demandé en base
	//var_dump($_GET['id_produit']);
	
	// si il n'y a pas de resultat dans $resultat, on redirige vers la boutique (=produit inexistant) :
	if($resultat->rowCount() == 0) {
		header('location:index.php');
		exit();
	}
	
	// 2- affichage et mis en forme des infos sur le produit :
	$produit = $resultat->fetch(PDO::FETCH_ASSOC);  // un seul produit dans $resultat car sélectionné par l'id_salle

		//var_dump($produit);

	$contenu .= '<div class="row">
					<div class="col-lg-12">
						<h1 class="page-header">'. $produit['titre'] .'</h1>					
					</div>				
				</div>';
				
	$contenu .= '<div class="col-md-8">
					<img class="img-responsive" src="'. $produit['photo'] .'" alt="">
				</div>';
			
	$contenu .= '<div class="col-md-4">
					<h3>Description</h3>
					<p>'. $produit['description'] .'</p>
					
					<h3>Localisation</h3>	
						<p>Adresse :'. $produit['adresse'] . ' - '. $produit['ville'] .' </p>
						
					<h3>Informations complémentaires</h3>
		
					<p> Arrivée : '. $produit['date_arrivee'] . '<br>
						Départ : '. $produit['date_depart'] .'	</p>
		
					<p>Capacité : '. $produit['capacite'] .'</p>
					
					<p>Catégorie : '. $produit['categorie'] .'</p>
			
					<p class="lead">Prix :'. number_format($produit['prix'], 2, ',', '.') .' €</p>';   
										
					
		if (internauteEstConnecte()) {
	// 3- affichage du formulaire d'ajout du panier si produit disponible :
	if ($produit['etat'] == 'libre') {
		$contenu .= '<div class="col-md-4">';
		$contenu .= '<form method="post" action="panier.php">';
			$contenu .= '<input type="hidden" name="id_produit" value="'. $produit['id_produit'] .'">';	

			
			$contenu .= '<input type="submit" style="background: #286090; color:white" name="ajout_panier" value="ajouter au panier" class="btn">';			
		$contenu .='</form>';
		$contenu .='<br>'.'</div>';
		
		$contenu .= '
	<div class="form-container">
	  <form action="avis.php" method="post">
	   <div class="form-group">
	 <button class="btn btn-success" style="background: #286090; border-color:#286090" type="submit">Déposez un commentaire et une note</button>
        </div>
      </form>';	
	
	$contenu .='</div>';
		
		
	} else {
		$contenu .= '<div class="col-md-4">
						<p>Produit indisponible</p>
						</div>';
		
	}
	
}else{
	$contenu .= '
	
	<div class="form-container">
          <form action="connex.php" method="post">
        <div class="form-group">
          <button class="btn btn-success" style="background: #286090; border-color:#286090" type="submit"><span class="glyphicon glyphicon-off"></span>Connectez-vous</button>
        </div>
      </form>';	
	
	$contenu .='</div>';
	
}

	// 4-Lien retour vers la boutique :
	
	$requetePDOS = executeRequete("SELECT categorie FROM salle");
	$requete = $requetePDOS->fetch(PDO::FETCH_ASSOC);
	//var_dump($requete);
	
	$contenu .= '<div class="col-md-4">';
		$contenu .= '<br><a href="index.php?categorie='. $produit['categorie'] .'">Retour vers votre sélection</a>';
	$contenu .='</div>';  // on passe dans l'url la catégorie du produit en cours de consultation pour n'afficher que les produits issus de cette catégorie dans la boutique.php
	

//suggestions de produits

	//var_dump($produit);
	 $requete = executeRequete("SELECT p.id_produit, s.id_salle, s.photo, s.titre, s.categorie FROM produit p, salle s WHERE p.id_salle = s.id_salle AND s.categorie = :categorie  AND s.id_salle != :id_salle ORDER BY RAND() LIMIT 0,2", array('categorie' => $produit['categorie'], 'id_salle'=> $produit['id_salle']));
	
	while($resultat = $requete->fetch(PDO::FETCH_ASSOC)){
	//var_dump($resultat);
		
		$aside .='<div style="width: 25%; float:left; padding:5px;">';
		
			$aside .= '<a href="?id_produit=' .$resultat['id_produit'] .'"><img src=" '. $resultat['photo']. '" style="width:100%;";></a>';
			$aside .= '<h4>' . $resultat['titre'] .'</h4>';
		$aside .='</div>';
	
	}
	
} else {
	// si id_produit n'existe pas dans l'url, on redirige vers la boutique :
	header('location:index.php');
	exit();
	
}	

 // affichage de la confirmation de l'ajout de l'article au panier :
if (isset($_GET['statut_produit']) && $_GET['statut_produit'] == 'ajoute') {
	// on affiche le popup :
	$contenu_gauche = '<div class ="modal fade" id="myModal" role="dialog">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<h4 class="modal-title">
											Le produit a bien été ajouté au panier !
										</h4>
									</div>
									
									<div class="modal-body">
										<p><a href="panier.php"> Voir le panier</a></p>
										<p><a href="index.php"> Continuer ses achats</a></p>
									</div>
								</div>
							</div>
						</div>';
											
} 





//-------------------------- AFFICHAGE HTML------------------------

require_once('inc/haut.inc.php');
echo $contenu_gauche;
?>
	<!-- fiche détaillée du salle -->
	<div class="row">
		<?php echo $contenu; ?>
		</div>
		
	<!-- suggestions de salles -->
	<div class="row">
		<div class="col-lg-12">
			<h3 class="page-header">Suggestions de salles</h3>
		</div>
		<?php echo $aside; ?>
		
	</div>
	
	<script>
		$(function() {
			// afficher la fenetre modale de bootstrap :
			$("#myModal").modal("show");			
		});

	</script>
	
	

<?php
require_once('inc/bas.inc.php');