<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SALLEA</title>

  
    <link href="<?php echo RACINE_SITE. 'inc/css/bootstrap.min.css';?>" rel="stylesheet">

    <link href="<?php echo RACINE_SITE. 'inc/css/shop-homepage.css';?>" rel="stylesheet">
	
	 <link rel="stylesheet" href="<?php echo RACINE_SITE .'inc/css/jquery.datetimepicker.css';?>">
	
	<link href="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js">
    <script src="http://code.jquery.com/jquery-latest.js"></script>
	
	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
     <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<style>

.navbar-nav a{
	
	margin: 5px -7px !important;
}

</style>
</head>

<body>

    <!-- Navigation -->
    <nav class="navbar navbar-inverse navbar-fixed-top" style="background: #286090" role="navigation">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" style="color:black; font-size:30px" href="<?php echo RACINE_SITE. 'index.php';?>">SALLEA</a>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <?php
					
					if (internauteEstConnecteEtEstAdmin()) {  // un ADMIN
						// Affiche liens vers back-office :
					
					echo '<li><a href="'.RACINE_SITE.'admin/gestion_salles.php" style="color:white">Gestion Salles</a></li>';
						
					echo'<li><a href="'.RACINE_SITE.'admin/gestion_produits.php" style="color:white">Gestion Produits</a></li>';
						
					echo'<li><a href="'.RACINE_SITE.'admin/gestion_membre.php" style="color:white">Gestion Membres</a></li>';
						
					echo '<li><a href="'.RACINE_SITE.'admin/gestion_avis.php" style="color:white">Gestion Avis</a></li>';
						
					echo '<li><a href="'.RACINE_SITE.'admin/gestion_commandes.php" style="color:white">Gestion Commandes</a></li>';
										
					
					}
									
					if (internauteEstConnecte()) { // membre connecté
						
					echo '<li><a href="'.RACINE_SITE.'prof.php" style="color:white; font-size:20px">Profil</a></li>';
					echo '<li><a href="'.RACINE_SITE.'panier.php" style="color:white; font-size:20px">Panier</a></li>';

					echo '<li><a href="'.RACINE_SITE.'contact.php" style="color:white; font-size:20px">Contact</a></li>';
					echo '<li><a href="'.RACINE_SITE.'connex.php?action=deconnexion" style="color:white; font-size:20px">Déconnexion</a></li>';
				
					} else {
						// visiteur non connecté
						echo '<li><a href="'.RACINE_SITE.'index.php" style="color:white; font-size:20px">Accueil</a></li>';
						echo '<li><a href="'.RACINE_SITE.'qui_sommes_nous.php" style="color:white; font-size:20px">Qui sommes nous ?</a></li>';
						echo '<li><a href="'.RACINE_SITE.'inscrip.php" style="color:white; font-size:20px">Inscription</a></li>';
						echo '<li><a href="'.RACINE_SITE.'connex.php" style="color:white; font-size:20px">Connexion</a></li>';
						echo '<li><a href="'.RACINE_SITE.'contact.php" style="color:white; font-size:20px">Contact</a></li>';
					
					}
				
				
					?>
                </ul>
			</div> 
			</ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>
<!-- Contenu de la page -->
	<div class="container" style="min-height: 80vh;">
		<!-- ICI viendra le contenu spécifique à chaque page -->
	