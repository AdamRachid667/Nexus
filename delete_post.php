<?php
session_start();
require 'db.php';

// si pas connecte je redirige
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// je recupere l'id du post depuis l'URL (?id=...)
$post_id = $_GET['id'];
$pseudo = $_SESSION['user'];

// je supprime d'abord les commentaires et likes lies a ce post
mysqli_query($connexion, "DELETE FROM commentaires WHERE post_id = $post_id");
mysqli_query($connexion, "DELETE FROM likes WHERE post_id = $post_id");
// puis je supprime le post lui-meme (seulement si c'est moi l'auteur)
mysqli_query($connexion, "DELETE FROM posts WHERE id = $post_id AND auteur = '$pseudo'");

header('Location: index.php');
?>
