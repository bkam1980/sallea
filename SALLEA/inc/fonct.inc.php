<?php

function internauteEstConnecte(){
	// cette fonction indique si l'internaute est connecté (donc membre)
	
	if (isset($_SESSION['membre'])) {
		return true; // il est connecté	
	} else {
		return false; // il n'est pas connecté			
	}	
}


function internauteEstConnecteEtEstAdmin(){
	// si il est connecté et son statut vaut 1 dans la session :
	if (internauteEstConnecte() && $_SESSION['membre']['statut'] == 1) {
			return true; 
	} else {
		return false; 
	}		
}


function executeRequete($req, $param = array()){
	
	 // traitement des valeurs de l'array avec htmlspecialchars :
	 if (!empty($param)) {
		 foreach($param as $indice => $valeur) {
			 $param[$indice] = htmlspecialchars($valeur, ENT_QUOTES); 
		 }
		 // dans ce foreach, on prend pour chaque indice, la valeur que l'on passe dans htmlspecialchars (pout traiter les caractères spéciaux, y compris les guillemets et quotes ici), puis on remet le résultat du traitement dans l'array $param exactement au même indice. [je prends une valeur d'un endroit, je la traite, et je la remets là où je l'ai prise]
	 }
	 global $pdo;  // permet d'accéder à la variable qui représente la connexion à la bdd déclarée dans l'espace global de init.inc.php
	 
	 $r = $pdo->prepare($req);  // prépare la requête reçue par la fonction
	 $r->execute($param); // éxécute la requête préparée en lui indiquant la valeur des paramètres éventuels présents dans l'array $param
	 
	 //var_dump($r->errorInfo());  // pour voir l'array retourné par errorInfo()
	 if (!empty($r->errorInfo()[2])) {
		 die('Erreur rencontrée lors de la requête. <br> Message de l\'erreur : ' . $r->errorInfo()[2] . '<br> La requête est : ' . $req);  // si il y a une erreur, on arrête le script (die) et affiche les infos sur l'erreur
	 }
	 return $r;  // on retourne l'objet issu de la classe PDOStatement à l'endroit où la fonction est appelée

}

//------------------------ Les fonctions liées au panier ----------------------------
function creationDuPanier(){
	// si le panier n'existe pas, on le crée (vide pout l'instant) :
	if(!isset($_SESSION['panier'])){
		$_SESSION['panier'] = array();
		$_SESSION['panier']['titre'] = array();
		$_SESSION['panier']['id_produit'] = array();
		$_SESSION['panier']['quantite'] = array();
		$_SESSION['panier']['prix'] = array();
			
	}
}

function ajouterProduitDansPanier($titre, $id_produit, $quantite, $prix){
	creationDuPanier();
	
	// nous devons savoir si le produit ajouté est déjà dans le panier. Si oui, nous additionnons les quantités, sinon on ajoute le produit.
	$position_produit = array_search($id_produit, $_SESSION['panier']['id_produit']);  // array_search cherche le premier élément dans l'array spécifié, et retourne l'indice auquel il l'a trouvé, sinon false.
	
	if($position_produit === false) {  // On compare en triple égalité car on veut entrer dans la condition si et seulement si array_search renvoie un BOOLEEN FALSE. Ainsi, si un produit est en position 0 du panier, on n'entre pas dans cette condition mais dans le else (la valeur implicite de 0 étant false).
	
		// le produit n'est pas encore dans le panier : on l'y ajoute :
		$_SESSION['panier']['titre'][] = $titre;
		$_SESSION['panier']['id_produit'][] = $id_produit;
		$_SESSION['panier']['quantite'][] = $quantite;
		$_SESSION['panier']['prix'][] = $prix;
		
	} else {
		// le produit y est déjà : on ajoute la quantité :
		$_SESSION['panier']['quantite'][$position_produit] += $quantite;
		
	}		
}


function montantTotal() {
	$total =0;
	
	
	for($i = 0; $i < count($_SESSION['panier']['id_produit']); $i++) {
		// Tant que $i est inférieur au nombre de produits dans le panier, on additionne le prix de l'article fois sa quantité :
		$total += $_SESSION['panier']['quantite'][$i] * $_SESSION['panier']['prix'][$i];
	}
		return $total; // return permet de renvoyer la valeur de $total à l'endroit où la fonction montantTotal() est appelée (à la fin du panier). Pour mémoire, $total est ici une variable locale, non accessible à l'extérieur de la fonction.

}

function retirerProduitDuPanier($id_produit_a_supprimer) {
	// on cherche d'abord la position du produit à supprimer dans le panier :
	$position_produit = array_search($id_produit_a_supprimer, $_SESSION['panier']['id_produit']);  // $position_produit contient l'indice du produit trouvé dans le panier. Cette fonction renvoie false si elle ne trouve rien.
	
	if($position_produit !== false) {
		// si on a trouvé le produit dans le panier (la fonction n'a pas renvoyé un BOOLEEN false):
		array_splice($_SESSION['panier']['titre'], $position_produit, 1); // efface et remplace une portion de tableau à partir de l'indice $position_produit et sur 1 indice
		array_splice($_SESSION['panier']['id_produit'], $position_produit, 1);
		array_splice($_SESSION['panier']['quantite'], $position_produit, 1);
		array_splice($_SESSION['panier']['prix'], $position_produit, 1);	
	}		
}
