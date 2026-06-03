<?php
session_start();
require 'db.php';

// si pas connecte j'arrete
if (!isset($_SESSION['user'])) {
    echo json_encode(['error' => 'Non connecte']);
    exit;
}

// je recupere les donnees que le JavaScript m'envoie
$data = json_decode(file_get_contents('php://input'), true);
$post_id = $data['id'];
$texte = $data['texte'];
$auteur = $_SESSION['user'];

// si le commentaire est vide j'arrete
if ($texte == '') {
    echo json_encode(['error' => 'Texte vide']);
    exit;
}

// j'insere le commentaire dans ma table commentaires
mysqli_query($connexion, "INSERT INTO commentaires (post_id, auteur, texte) VALUES ($post_id, '$auteur', '$texte')");

// je renvoie le commentaire au JavaScript pour l'afficher direct sans recharger
$commentaire = [
    'auteur' => $auteur,
    'texte' => $texte
];

echo json_encode(['success' => true, 'commentaire' => $commentaire]);
?>
