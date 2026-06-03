<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$post_id = $_GET['id'];
$pseudo = $_SESSION['user'];

// Supprimer seulement si c'est l'auteur
mysqli_query($connexion, "DELETE FROM commentaires WHERE post_id = $post_id");
mysqli_query($connexion, "DELETE FROM likes WHERE post_id = $post_id");
mysqli_query($connexion, "DELETE FROM posts WHERE id = $post_id AND auteur = '$pseudo'");

header('Location: index.php');
?>
