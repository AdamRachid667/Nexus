<?php
session_start();

// Si pas connecte, retour a l'accueil
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$titre = $_POST['titre'];
$contenu = $_POST['contenu'];

// Verifier que les champs sont remplis
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

// Charger les posts
$posts = json_decode(file_get_contents('data/posts.json'), true);

// Ajouter le nouveau post
$posts[] = [
    'id' => uniqid(),
    'auteur' => $_SESSION['user'],
    'titre' => $titre,
    'contenu' => $contenu,
    'image' => $image,
    'date' => time(),
    'likes' => 0,
    'liked_by' => [],
    'commentaires' => []
];

// Sauvegarder
file_put_contents('data/posts.json', json_encode($posts));

header('Location: index.php');
?>
