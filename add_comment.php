<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user'])) {
    echo json_encode(['error' => 'Non connecte']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$post_id = $data['id'];
$texte = $data['texte'];
$auteur = $_SESSION['user'];

if ($texte == '') {
    echo json_encode(['error' => 'Texte vide']);
    exit;
}

mysqli_query($connexion, "INSERT INTO commentaires (post_id, auteur, texte) VALUES ($post_id, '$auteur', '$texte')");

$commentaire = [
    'auteur' => $auteur,
    'texte' => $texte
];

echo json_encode(['success' => true, 'commentaire' => $commentaire]);
?>
