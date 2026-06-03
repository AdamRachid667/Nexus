<?php
session_start();
require 'db.php';

// si pas connecte, je redirige vers login
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// je recupere ce que l'utilisateur a ecrit dans le formulaire
$titre = $_POST['titre'];
$contenu = $_POST['contenu'];

// si c'est vide je retourne a l'accueil
if ($titre == '' || $contenu == '') {
    header('Location: index.php');
    exit;
}

// je gere l'upload de l'image
$image = '';
if ($_FILES['image']['error'] == 0) {
    // uniqid() ca genere un nom unique pour pas avoir 2 images avec le meme nom
    $nom = uniqid() . '.jpg';
    // je deplace l'image dans mon dossier uploads/
    move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $nom);
    $image = 'uploads/' . $nom;
}

// j'insere le post dans ma table posts
$auteur = $_SESSION['user'];
mysqli_query($connexion, "INSERT INTO posts (auteur, titre, contenu, image) VALUES ('$auteur', '$titre', '$contenu', '$image')");

// je retourne a l'accueil
header('Location: index.php');
?>
