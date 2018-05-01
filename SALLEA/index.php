<?php
require_once('inc/init.inc.php');
//------------------ TRAITEMENT ------------------------------
// 1- Affichage des catégories :

$categorie_des_salles = executeRequete("SELECT DISTINCT categorie FROM salle ORDER BY categorie");

$contenu_gauche .='<p class="lead">Catégorie</p>';
$contenu_gauche .='<div class="list-group">';
	$contenu_gauche .= '<a href="?categorie=all" class="list-group-item">Tous</a>'; // lien pour toutes les catégories
	
	while($cat = $categorie_des_salles->fetch(PDO::FETCH_ASSOC)) {
		//var_dump($cat);
			$contenu_gauche .= '<a href="?categorie='.$cat['categorie'] .'" class="list-group-item">'. $cat['categorie'] .'</a>';
	}

$contenu_gauche .= '</div>';

$categorie_des_villes = executeRequete("SELECT DISTINCT ville FROM salle ORDER BY ville");

$contenu_gauche .='<p class="lead">Ville</p>';
$contenu_gauche .='<div class="list-group">';
	$contenu_gauche .= '<a href="?ville=all" class="list-group-item">Tous</a>'; // lien pour toutes les catégories
	
	while($vil = $categorie_des_villes->fetch(PDO::FETCH_ASSOC)) {
		//var_dump($cat);
			$contenu_gauche .= '<a href="?ville='.$vil['ville'] .'" class="list-group-item">'. $vil['ville'] .'</a>';
	}

$contenu_gauche .= '</div>';

$capacite = executeRequete("SELECT DISTINCT capacite FROM salle ORDER BY capacite");

$contenu_gauche .='<p class="lead">Capacite</p>';
$contenu_gauche .='<div class="list-group">';
$contenu_gauche .= ' <select class="form-control" name="capacite" id="champ-capacite">
          <option value="">Toutes</option>
                      <option value="5">5</option>
                      <option value="10">10</option>
                      <option value="20">20</option>
                      <option value="30">30</option>
                      <option value="40">40</option>
                      <option value="50">50</option>
                      <option value="100">100</option>
                  </select>'; 

$contenu_gauche .= '</div>';

$prix = executeRequete("SELECT DISTINCT prix FROM produit");

$contenu_gauche .='<p class="lead">Prix</p>';
$contenu_gauche .='<div class="list-group">';
	$contenu_gauche .= '<input class="" type="range" min="200" max="2000" step="10" value="" name="prix" id="champ-prix"></a>'; // lien pour toutes les catégories
	

$contenu_gauche .= '</div>';

$dates = executeRequete("SELECT date_arrivee, date_depart FROM produit");

$contenu_gauche .='<p class="lead">Période</p>';


$contenu_gauche .='<div class="list-group">';
	$contenu_gauche .= '
			<div class="form-group">
				<label for="date_arrivee">Date d\'arrivée :</label>
				<div class="input-group">
				 <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
				<input class="form-control" type="text" name="date_arrivee" id="date_arrivee" placeholder="JJ/MM/AAAA" required="required"/>
				</div>
			</div>';
	
	$contenu_gauche .= '
			<div class="form-group">
				<label for="date_depart">Date de départ :</label>
				<div class="input-group">
				 <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
				<input class="form-control" type="text" name="date_depart" id="date_depart" placeholder="JJ/MM/AAAA" required="required"/>
				</div>
			</div>';
			  

$contenu_gauche .= '</div>';

$contenu_gauche .=' <button class="btn btn-primary" type="submit" name="bouton-filtre">Filtrer</button>';

// 2- affichage des produits en fonction de la catégorie :
if (isset($_GET['categorie']) && $_GET['categorie'] != 'all'){
	// requête sur la catégorie sélectionnée :
	$donnees = executeRequete("SELECT p.id_produit, p.prix, p.date_arrivee, p.date_depart, s.id_salle, s.titre, s.description, s.photo, s.pays, s.ville, s.adresse, s.cp, s.capacite,s.categorie FROM salle s, produit p WHERE s.id_salle = p.id_salle AND categorie = :categorie", array('categorie' => $_GET['categorie']));

		
} elseif(isset($_GET['ville']) && $_GET['ville'] != 'all') {
	// requête sur la catégorie sélectionnée :
	$donnees = executeRequete("SELECT p.id_produit, p.prix, DATE_FORMAT (p.date_arrivee, '%d/%m/%Y') AS date_arrivee, DATE_FORMAT(p.date_depart, '%d/%m/%Y') AS date_depart , s.id_salle, s.titre, s.description, s.photo, s.pays, s.ville, s.adresse, s.cp, s.capacite,s.categorie FROM salle s, produit p WHERE s.id_salle = p.id_salle AND ville = :ville", array('ville' => $_GET['ville']));
		//var_dump($_GET['ville']);
		
}
elseif(isset($_GET['capacite']) && $_GET['capacite'] != 'all') {
	// requête sur la catégorie sélectionnée :
	$donnees = executeRequete("SELECT p.id_produit, p.prix, DATE_FORMAT (p.date_arrivee, '%d/%m/%Y') AS date_arrivee, DATE_FORMAT(p.date_depart, '%d/%m/%Y') AS date_depart , s.id_salle, s.titre, s.description, s.photo, s.pays, s.ville, s.adresse, s.cp, s.capacite,s.categorie FROM salle s, produit p WHERE s.id_salle = p.id_salle AND capacite = :capacite", array('capacite' => $_GET['capacite']));
		//var_dump($_GET['capacite']);
		
} else {
	$donnees = executeRequete("SELECT p.id_produit, p.prix, DATE_FORMAT (p.date_arrivee, '%d/%m/%Y') AS date_arrivee, DATE_FORMAT(p.date_depart, '%d/%m/%Y') AS date_depart , s.id_salle, s.titre, s.description, s.photo, s.pays, s.ville, s.adresse, s.cp, s.capacite,s.categorie FROM salle s, produit p WHERE s.id_salle = p.id_salle ");	
}
		
	while($produit = $donnees->fetch(PDO::FETCH_ASSOC)) {
	//var_dump($produit);
	
	$contenu_droite .= '<div class="col-sm-3  col-lg-3 col-md-3">';
		$contenu_droite .= '<div class="thumbnail">';
			$contenu_droite .= '<a href="fiche_produit.php?id_produit='.$produit['id_produit'] .'"><img src="'.$produit['photo'] .'" width="130" height="80"></a>';
			
			$contenu_droite .='<div class="caption">';
			
			$contenu_droite .= '<h4 class="pull-right">'.$produit['prix'] .' €</h4>';
				
				$contenu_droite .= '<h4>' . $produit['titre'] .'</h4>';
				$contenu_droite .= '<p>' . $produit['description'] .'</p>';
				
				$contenu_droite .= '<p>' .'Arrivée :'.' '. $produit['date_arrivee'] . ' départ :'. ' '.$produit['date_depart'] .'</p>';
			
			
			$contenu_droite .= '</div>';
		$contenu_droite .= '</div>';
	$contenu_droite .= '</div>';
	
}

//-------------------------- AFFICHAGE ------------------------
require_once('inc/haut.inc.php');
echo $contenu;
?>


    <!-- Page Content -->


                <div class="row carousel-holder">

                    <div class="col-md-12">
                        <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
                            <ol class="carousel-indicators">
                                <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
                                <li data-target="#carousel-example-generic" data-slide-to="1"></li>
                                <li data-target="#carousel-example-generic" data-slide-to="2"></li>
                            </ol>
                            <div class="carousel-inner">
                                <div class="item active">
                                    <img class="slide-image" src="photo/slider1.jpg" alt="monImage">
                                </div>
                                <div class="item">
                                    <img class="slide-image" src="photo/slider2.jpg" alt="monImage">
                                </div>
                                <div class="item">
                                    <img class="slide-image" src="photo/slider3.jpg" alt="monImage">
                                </div>
                            </div>
                            <a class="left carousel-control" href="#carousel-example-generic" data-slide="prev">
                                <span class="glyphicon glyphicon-chevron-left"></span>
                            </a>
                            <a class="right carousel-control" href="#carousel-example-generic" data-slide="next">
                                <span class="glyphicon glyphicon-chevron-right"></span>
                            </a>
                        </div>
                    </div>

                </div>

                <div class="row">
		            <div class="col-md-3">
		
			        <?php echo $contenu_gauche;?>
		
		        </div>
		
		        <div class="col-md-9">
			       <div class="row">
				   <?php echo $contenu_droite;?>
			    </div>
		  </div>
	</div>
      

    <?php
require_once('inc/bas.inc.php');