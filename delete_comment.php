<?php
session_start();
require 'db.php';

// si pas connecte j'arrete
if (!isset($_SESSION['user'])) {
    echo json_encode(['error' => 'Non connecte']);
    exit;
}

// je recupere l'id du commentaire depuis le JavaScript
$data = json_decode(file_get_contents('php://input'), true);
$comment_id = $data['id'];
$pseudo = $_SESSION['user'];

// je supprime le commentaire (seulement si c'est le mien)
mysqli_query($connexion, "DELETE FROM commentaires WHERE id = $comment_id AND auteur = '$pseudo'");

echo json_encode(['success' => true]);
?>
