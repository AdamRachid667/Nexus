<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$titre = $_POST['titre'];
$contenu = $_POST['contenu'];

if ($titre == '' || $contenu == '') {
    header('Location: index.php');
    exit;
}

// Upload image
$image = '';
if ($_FILES['image']['error'] == 0) {
    $nom = uniqid() . '.jpg';
    move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $nom);
    $image = 'uploads/' . $nom;
}

$auteur = $_SESSION['user'];
mysqli_query($connexion, "INSERT INTO posts (auteur, titre, contenu, image) VALUES ('$auteur', '$titre', '$contenu', '$image')");

header('Location: index.php');
?>
