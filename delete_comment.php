<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user'])) {
    echo json_encode(['error' => 'Non connecte']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$comment_id = $data['id'];
$pseudo = $_SESSION['user'];

// Supprimer seulement si c'est l'auteur
mysqli_query($connexion, "DELETE FROM commentaires WHERE id = $comment_id AND auteur = '$pseudo'");

echo json_encode(['success' => true]);
?>
