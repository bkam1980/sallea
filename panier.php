<?php
require_once('inc/init.inc.php');

//-------------------------- TRAITEMENT------------------------


// 2 - ajout d'un produit au panier :
//var_dump($_POST);
if(isset($_POST['ajout_panier'])) {  // on a cliqué sur "ajouter au panier"
		// on va chercher en BDD les infos sur le produit ajouté :
		$resultat = executeRequete("SELECT * FROM produit WHERE id_produit = :id_produit", array('id_produit' => $_POST['id_produit']));
		
		$produit = $resultat->fetch(PDO::FETCH_ASSOC);  // on fetch l'objet $resultat en array associatif pour exploiter ses données :

		ajouterProduitDansPanier($produit['titre'], $produit['id_produit'], $_POST['quantite'], $produit['prix']);
		
	// on redirige vers la fiche produit :
	header('location:fiche_produit.php?statut_produit=ajoute&id_produit=' . $_POST['id_produit']); // on repasse l'id_produit à fiche_produit.php pour pouvoir afficher la page du produit concerné
	
}

// 3-- Vider le panier :
if (isset($_GET['action']) && $_GET['action'] == 'vider') {
	// on supprime le panier de la session :
	unset($_SESSION['panier']); // unset() supprime l'indice 'panier' de l'array $_SESSION (et son contenu).
}

// 4-- Supprimer un article du panier :
if (isset($_GET['action']) && $_GET['action'] == 'supprimer_article' && isset($_GET['articleASupprimer'])) {
	
	retirerProduitDuPanier($_GET['articleASupprimer']); // appelle la fonction en lui passant l'id du produit à supprimer en argument
}

// 5-- Validation du panier :
// Dans la réalité, il faudrait ici revalider que le stock des produits du panier sont toujours disponibles au moment de la validation du panier. Si ce n'était pas le cas, il faudrait modifier les quantités dans le panier ET prévenir l'internaute par un message d'alerte.

if (isset($_POST['valider'])){
var_dump($_SESSION);
	// si on a cliqué sur 'valider le panier' :
	$id_membre = $_SESSION['membre']['id_membre'];  // on récupère l'id du membre pour pouvoir remplir la table commande
	
	// on peut insérer la commande en BDD :
	// Conversion date arrivee pour BDD avant l'insert en BDD

      // $dateA = str_replace('/', '-', $_POST['date_enregistrement']);
      $Date_enregistrement = date('Y-m-d H:i');
	
	executeRequete("INSERT INTO commande (id_commande, id_membre, id_produit, date_enregistrement) VALUES(:id_commande, :id_membre, :id_produit, NOW())", array(
		'id_commande' => $id_commande,
		'id_membre' => $id_membre, 
		'id_produit'=> $id_produit,
		'date_enregistrement' => $Date_enregistrement));
	
	
	
	// pour insérer le détail d'une commande, on récupère l'id de la commande insérée:
	$id_commande = $pdo->lastInsertId();
	var_dump($id_commande);
	
	// met à jour la table commande : on insère tous les id_produit / id_membre/ prix de la commande précédemment insérée :
	for ($i = 0; $i < count($_SESSION['panier']['id_produit']); $i++) {
		
		$id_produit = $_SESSION['panier']['id_produit'][$i];
		
		executeRequete("INSERT INTO commande (id_commande, id_membre, id_produit, date_enregistrement) VALUES(:id_commande, :id_membre, :id_produit, NOW())", array(
		'id_commande' => $id_commande,
		'id_membre' => $id_membre, 
		'id_produit'=> $id_produit,
		'date_enregistrement' => $Date_enregistrement));
	
	}
	// après les insertions en BDD, on vide le panier :
	unset($_SESSION['panier']);
	
	$contenu .='<div class="bg-success">Merci pour votre commande. Votre numéro de suivi est le '. $id_commande .'</div>';
	
	
	
} // fin de la condition (voir openClassroom)







//-------------------------- AFFICHAGE ------------------------
require_once('inc/haut.inc.php');
echo $contenu;

echo '<h2>Voici votre panier</h2>';

// on affiche le panier s'il n'est pas vide :
if(empty($_SESSION['panier']['id_produit'])) {
	// panier vide :
	echo '<p>Votre panier est vide.</p>';	
} else {
	echo '<table class="table">';
	echo '<tr class="info">
			
			<th>id_produit</th>
			
			<th>Prix unitaire</th>
			<th>Action</th>	
		  </tr>';
		  
		 //var_dump($_SESSION);
	// affichage des lignes de produits :
	for($i =0; $i < count($_SESSION['panier']['id_produit']); $i ++) {
		echo '<tr>';
			
			echo '<td>'.$_SESSION['panier']['id_produit'][$i] .'</td>';
		
			echo '<td>'.$_SESSION['panier']['prix'][$i] .' €</td>';
			echo '<td>
					<a href="?action=supprimer_article&articleASupprimer='. $_SESSION['panier']['id_produit'][$i] .'">supprimer article</a>
					</td>';		
		echo '</tr>';
	}  // cette boucle parcourt les 4 arrays titre, id_produit, quantite, et prix simultanément , grâce à l'indice $i. Concernant le lien "supprimer article", on passe dans l'url (en GET) l'action "supprimer_article" ET l'id du produit à supprimer dans "articleASupprimmer".
	

		  // si internaute connecté, on affiche le bouton "valider le panier", sinon on l'invite à s'inscrire ou se connecter :
		  if (internauteEstConnecte()) {
			  echo '<form method="post" action="">
					<tr class="text-center">
						<td colspan="5">
							<input type="submit" name="valider" value="valider le panier" class="btn">
						</td>
					</tr>
					</form>';
			  
		  } else {
			  // si pas connecté :
		
				echo '<tr class="text-center">
						<td colspan="5">
							Veuillez vous <a href="">inscrire</a> ou vous <a href="connexion.php"> connecter </a> afin de pouvoir valider le panier
						  </td>
						  </tr>';		  
		  }
		  
		  
		  // Ajout du lien "vider le panier" :
		  echo '<tr class="text-center">
						<td colspan="5">
							Veuillez vous <a href="?action=vider">vider le panier</a> 
						  </td>
						  </tr>';		  
	
	echo '</table>';
	
}

require_once('inc/bas.inc.php');