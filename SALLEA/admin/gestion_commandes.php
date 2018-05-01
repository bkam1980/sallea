<?php
require_once('../inc/init.inc.php');
//------------------ TRAITEMENT ------------------------------

// 1 - VERIFICATION ADMIN
if (!internauteEstConnecteEtEstAdmin()) {
	header('location:../connex.php');  // je remonte dans le dossier parent avec ../ puis descend vers le fichier connexion.php
	exit();
}


// 2 - ajout d'un produit a la commande :

if(isset($_POST['ajout_commande'])) {  
		$resultat = executeRequete("SELECT * FROM produit WHERE id_produit = :id_produit", array('id_produit' => $_POST['id_produit']));
		
		$produit = $resultat->fetch(PDO::FETCH_ASSOC);  // on fetch l'objet $resultat en array associatif pour exploiter ses données :
		 
		ajouterProduitDansCommande($produit['titre'], $produit['id_produit'], $_POST['quantite'], $produit['prix']);
		
	// on redirige vers la fiche produit :
	header('location:fiche_produit.php?statut_produit=ajoute&id_produit=' . $_POST['id_produit']); // on repasse l'id_produit à fiche_produit.php pour pouvoir afficher la page du produit concerné
	
}


// 7 - SUPPRESSION d'une commande
if (isset($_GET['action']) && $_GET['action'] == 'suppression' && isset($_GET['id_commande'])){
	
	// on sélectionne en base la photo (url) pour pouvoir supprimer le fichier physique du serveur :
	$resultat = executeRequete("SELECT id_commande FROM commande WHERE id_commande = :id_commande", array('id_commande' => $_GET['id_commande']));
	
	$commande_a_supprimer = $resultat->fetch(PDO::FETCH_ASSOC); // pas de boucle while car on est certain de n'avoir qu'un seul résultat au plus
	
	//var_dump($commande_a_supprimer);

	executeRequete("DELETE FROM commande WHERE id_commande =:id_commande", array('id_commande'=>$_GET['id_commande']));
	$contenu .='<div class="bg-success">La commande a bien été supprimé.</div>';
	$_GET['action'] = 'affichage';  // pour pouvoir entrer dans la condition du point 6 ci-dessous qui affiche le tableau HTML des commandes
}



// 2 - ONGLETS AFFICHAGE OU AJOUT (? vaut GET)
$contenu .= '<ul class=" nav nav-tabs">
				<li class=""><a href="?action=affichage">Affichage des commandes</a></li> 
				<li class=""><a href="?action=ajout">Ajout d\'une commande</a></li>
			</ul>';
			
// 4- ENREGISTREMENT DU commande
if(!empty($_POST)){ // si le formulaire est soumis
	//var_dump($_POST);
	
	// Conversion date arrivee pour BDD avant l'insert en BDD

        $dateA = str_replace('/', '-', $_POST['date_enregistrement']);
        $Date_enregistrement = date('Y-m-d H:i', strtotime($dateA));
        
      
	
	
	// 4- suite : enregistrement du commande en base :
	executeRequete("REPLACE INTO commande (id_commande, id_membre, id_produit, date_enregistrement) VALUES(:id_commande, :id_membre, :id_produit, :date_enregistrement)",
	array('id_commande'=>$_POST['id_commande'],  
		  'id_membre'=>$_POST['id_membre'],
		  'id_produit'=>$_POST['id_produit'],
		  'date_enregistrement'=>$Date_enregistrement,
	
	));
	

	$contenu .= '<div class="bg-success">Le commande a été enregistré</div>';
	$_GET['action'] ='affichage'; // on met 'affichage' dans $_GET['action'] pour déclencher l'affichage du tableau HTML des commandes et ne plus afficher le formulaire 
}	
	
	 // fin du if(!empty($_POST))

		 
		 
// 6- AFFICHAGE DES commandeS DANS UNE TABLE HTML :
if((isset($_GET['action']) && $_GET['action'] == 'affichage') || !isset($_GET['action'])) { // si l'affichage est demandée Ou on arrive sur la page pour la première fois ($_GET['action'] n'existe pas)
	
//$resultat = executeRequete("SELECT * FROM commande"); // sélectionne tous les commandes

	
	// affichage des lignes :
	
	$prodPDOS = executeRequete("SELECT c.id_commande, m.email, m.id_membre, s.id_salle,  s.titre, p.id_produit, p.date_arrivee, p.date_depart,  p.prix, c.date_enregistrement
		FROM commande c, membre m, produit p, salle s
		WHERE 
		c.id_membre = m.id_membre
		AND c.id_produit = p.id_produit
		AND p.id_salle = s.id_salle");
		
	
	$contenu .= '<h3>Affichage des commandes</h3>';
$contenu .= '<p>Nombre de commandes : '. $prodPDOS->rowCount() .'</p>';
$contenu .= '<table class="table">';
	// affichage des entêtes :
	$contenu .='<tr>';
		for($i = 0; $i < $prodPDOS->columnCount(); $i++) {
			$colonne = $prodPDOS->getColumnMeta($i); // $resultat est un objet issu de la classe PDOStatement sur lequel on applique une méthode getColumnMeta($indice) qui retourne un array
			//var_dump($colonne);
			
			if ($colonne['name'] != 'id_salle' && $colonne['name'] != 'email' && $colonne['name'] != 'titre' && $colonne['name'] != 'date_arrivee' && $colonne['name'] != 'date_depart'){
				$contenu .= '<th>'. ucfirst($colonne['name']) .'</th>';  // c'est à l'indice 'name' de cet array que l'on trouve le nom du champ. ucfirst() pour mettre la première lettre en majuscule
			
			}	
		}					
			$contenu .= '<th> Actions </th>';

	$contenu .='</tr>';
	

	while($ligne = $prodPDOS->fetch(PDO::FETCH_ASSOC)){
		$contenu .='<tr>';
		//var_dump($ligne);
		

		$idsalle = '';
		$salletitre = '';
		$emailmembre ='';
		
		
		foreach($ligne as $indice => $information) {  // "parcourt $ligne par ses indices auxquels j'associe la valeur"
			//var_dump($information);
			if ($indice == 'date_enregistrement') {
		$information = date('d/m/Y H:i', strtotime($information));
		$contenu .='<td>'."$information ". '</td>';
	}
		
			elseif ($indice != 'id_salle' && $indice != 'email'  &&  $indice != 'titre' && $indice != 'id_produit' && $indice != 'date_arrivee' && $indice != 'date_depart' && $indice != 'email' && $indice != 'id_membre'  ){
				//var_dump($indice);
				$contenu .='<td>'. $information .'</td>'; 
				// $information contient les valeurs
				//var_dump($information);
				
			} 
		
			elseif ($indice == 'id_salle' ){
				// on stocke la valeur du id_salle ciblé
				$idsalle = $information;
				//var_dump($idsalle);
				
			} elseif ($indice == 'titre' ){
				
				$salletitre = $information;
				
				//var_dump($salletitre);
			} elseif ($indice == 'date_arrivee' ){
				
				$datearrivee = date('d/m/Y H:i', strtotime($information));
				
				
			} elseif ($indice == 'date_depart' ){
				
				$datedepart = date('d/m/Y H:i', strtotime($information));
											
				$contenu .='<td>'."$idsalle - $salletitre - <br> $datearrivee au $datedepart ". '</td>';  // $information contient les valeurs
								
			}		
			
			elseif ($indice == 'email' ){			
				$emailmembre = $information;	
				
				
			}
			
			elseif ($indice == 'id_membre' ){			
			
			$contenu .='<td>'."$information - $emailmembre". '</td>';  // $information contient les valeurs
				
			}
		
			
		}
		
		// Ajoute les liens modifier et supprimer :

		$contenu .= '<td>
						<a href="?action=modification&id_commande='. $ligne['id_commande'] .'">modifier</a> -
						<a href="?action=suppression&id_commande='. $ligne['id_commande'] .'" onclick="return(confirm(\'Etes-vous certain de vouloir supprimer cette commande ?\'));">supprimer</a>	
					</td>';

					
					// dans les href, on concatène $ligne['id_commande'] pour avoir l'id du commande modifié ou supprimé dans $_GET. Ainsi, on peut cibler le DELETE ou le REPLACE sur cet id en particulier. Dans le onclick : la fonction confirm() retourne true (si l'internaute clique "ok") ou false (s'il clique "annuler"). Ainsi, "return true" ne bloque pas le lien <a>, alors que "return false" bloque le lien <a> tel que le ferait un e.preventDefault(). 
		
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
	if(isset($_GET['id_commande'])) {
		// si il y a un id_commande passé dans l'url, on sélectionne en BDD les infos de ce commande :
		$resultat = executeRequete("SELECT * FROM commande WHERE id_commande = :id_commande, id_membre = :id_membre", array('id_commande' => $_GET['id_commande'] ,'id_membre'=>$_GET['id_membre']));
		//var_dump($resultat);
		
		$commande_actuel = $resultat->fetch(PDO::FETCH_ASSOC); // pas de boucle while car il n'y a qu'un seul commande dans cette requête
		//var_dump($commande_actuel);
		//var_dump($commande_actuel);
	}
	
?>
<div class="container" style="min-height: 80vh;">
<h3>Formulaire d'ajout ou de modification d'une commande</h3>
<form method="post" action="" enctype="multipart/form-data">


	<label for="id_commande">Id_commande</label><br>
	<input  id="id_commande" name="id_commande" value="<?php echo $commande_actuel['id_commande'] ?? 0; ?>"><!-- type "hidden" pour ne pas afficher le champ. Il contient l'id_commande qu'on utilisera en BDD. valeur 0 si on est en ajout, pour s'assurer que cet id n'existe pas en base, et donc utiliser un REPLACE INTO en tant que INSERT -->
	<br><br>
	  <label for="id_membre">Id_membre</label><br>
	  <input  id="id_membre" name="id_membre" value="<?php echo $commande_actuel['id_membre'] ?? 0; ?>"><!-- type "hidden" pour ne pas afficher le champ. Il contient l'id_produit qu'on utilisera en BDD. valeur 0 si on est en ajout, pour s'assurer que cet id n'existe pas en base, et donc utiliser un REPLACE INTO en tant que INSERT -->
	<br><br>  
	  <label for="id_produit">Id_produit</label><br>
	   <input  id="id_produit" name="id_produit" value="<?php echo $commande_actuel['id_produit'] ?? 0; ?>"><!-- type "hidden" pour ne pas afficher le champ. Il contient l'id_produit qu'on utilisera en BDD. valeur 0 si on est en ajout, pour s'assurer que cet id n'existe pas en base, et donc utiliser un REPLACE INTO en tant que INSERT -->
	<br><br>
	
	
		
		</select>

      <!-- Date d'enregistrement -->
      <div class="form-group">
				<label for="date_enregistrement">Date d'enregistrement :</label>
				<div class="input-group">
				 <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
				<input  class="form-control" type="text" style=" width:120px" name="date_enregistrement" id="date_arrivee" placeholder="JJ/MM/AAAA" required="required"/>
				</div>
	  </div>

	<br>

	
	

	<input type="submit" value="valider" class="btn">
</form>


</div>

<?php
endif;
require_once('../inc/bas.inc.php');

