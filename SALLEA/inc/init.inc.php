<?php

/*
Ce fichier sera inclus dans tous les scripts (hors les .inc eux-mêmes) pour initialiser les éléments suivants :
- création ou ouverture de session
- connexion à la BDD 'site'
- définition du chemin du site
- inclusion du fichier fonction.inc.php
*/

// Les Sessions :
session_start();

// Connexion à la BDD locale:
$pdo = new PDO('mysql:host=localhost;dbname=salle_a', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));


// Connexion à la BDD en ligne:
//$pdo = new PDO('mysql:host=tmorelfrftlmaata.mysql.db;dbname=tmorelfrftlmaata', 'tmorelfrftlmaata', 'Bardamu67', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

//Définition du chemin du site dans une constante :
define('RACINE_SITE', '/SALLEA/');  // indique le dossier dans lequel se situe le site sans 'localhost'


// Déclaration de variables d'affichage utilisées partout dans le site :
$contenu = '';
$contenu_gauche = '';
$contenu_droite = '';


// Inclusions du fichier fonction.inc.php :
require_once('fonct.inc.php');



//***********************************
