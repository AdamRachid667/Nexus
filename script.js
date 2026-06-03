// === VALIDATION DU FORMULAIRE DE POST ===
// je verifie que le formulaire est bien rempli avant de l'envoyer
var formPost = document.getElementById('form-post');
if (formPost) {
    formPost.addEventListener('submit', function(e) {
        var titre = document.getElementById('titre').value.trim();
        var contenu = document.getElementById('contenu').value.trim();
        var erreur = document.getElementById('error-msg');

        // je verifie que les champs sont pas vides
        if (titre == '' || contenu == '') {
            e.preventDefault(); // j'empeche l'envoi du formulaire
            erreur.textContent = 'Le titre et le contenu sont obligatoires !';
            erreur.style.display = 'block';
            return;
        }

        // je verifie la longueur du titre
        if (titre.length > 100) {
            e.preventDefault();
            erreur.textContent = 'Le titre ne peut pas depasser 100 caracteres !';
            erreur.style.display = 'block';
            return;
        }

        // je verifie la longueur du contenu
        if (contenu.length > 2000) {
            e.preventDefault();
            erreur.textContent = 'Le contenu ne peut pas depasser 2000 caracteres !';
            erreur.style.display = 'block';
            return;
        }

        // je verifie l'image si y en a une
        var image = document.getElementById('image');
        if (image.files.length > 0) {
            var fichier = image.files[0];
            var extensions = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (extensions.indexOf(fichier.type) == -1) {
                e.preventDefault();
                erreur.textContent = 'Format d\'image pas supporte ! (JPG, PNG, GIF, WEBP)';
                erreur.style.display = 'block';
                return;
            }
            if (fichier.size > 5000000) {
                e.preventDefault();
                erreur.textContent = 'L\'image doit pas depasser 5 Mo !';
                erreur.style.display = 'block';
                return;
            }
        }

        // tout est bon, je cache l'erreur
        erreur.style.display = 'none';
    });
}

// === VALIDATION DU FORMULAIRE D'INSCRIPTION ===
var formRegister = document.getElementById('form-register');
if (formRegister) {
    formRegister.addEventListener('submit', function(e) {
        var pseudo = formRegister.querySelector('input[name="pseudo"]').value.trim();
        var mdp = formRegister.querySelector('input[name="mdp"]').value;
        var mdp2 = formRegister.querySelector('input[name="mdp2"]').value;

        if (pseudo.length < 3 || pseudo.length > 20) {
            e.preventDefault();
            alert('Le pseudo doit faire entre 3 et 20 caracteres.');
            return;
        }

        if (mdp.length < 4) {
            e.preventDefault();
            alert('Le mot de passe doit faire au moins 4 caracteres.');
            return;
        }

        if (mdp != mdp2) {
            e.preventDefault();
            alert('Les mots de passe correspondent pas !');
            return;
        }
    });
}

// === SYSTEME DE LIKE ===
// j'envoie une requete au serveur sans recharger la page (AJAX avec fetch)
function likePost(id) {
    fetch('like.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({id: id})
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.likes !== undefined) {
            // je mets a jour le nombre de likes affiche
            document.getElementById('likes-' + id).textContent = data.likes;
        } else if (data.error == 'Non connecte') {
            alert('Connecte-toi pour liker !');
        }
    });
}

// === AFFICHER/MASQUER LES COMMENTAIRES ===
function toggleComments(id) {
    var section = document.getElementById('comments-' + id);
    if (section.style.display == 'none') {
        section.style.display = 'block';
    } else {
        section.style.display = 'none';
    }
}

// === AJOUTER UN COMMENTAIRE ===
// pareil, j'envoie en AJAX sans recharger la page
function addComment(e, postId) {
    e.preventDefault(); // j'empeche le rechargement de la page
    var input = document.getElementById('comment-input-' + postId);
    var texte = input.value.trim();

    if (texte == '') return;

    if (texte.length > 500) {
        alert('Commentaire trop long (max 500 caracteres) !');
        return;
    }

    fetch('add_comment.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({id: postId, texte: texte})
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {
            // j'ajoute le commentaire directement dans la page
            var liste = document.getElementById('comments-' + postId).querySelector('.comments-list');
            var div = document.createElement('div');
            div.className = 'comment';
            div.innerHTML = '<strong>' + escapeHtml(data.commentaire.auteur) + '</strong>' +
                '<span class="comment-date"> a l\'instant</span>' +
                '<p>' + escapeHtml(data.commentaire.texte) + '</p>';
            liste.insertBefore(div, liste.firstChild);
            input.value = ''; // je vide le champ
        } else if (data.error == 'Non connecte') {
            alert('Connecte-toi pour commenter !');
        }
    });
}

// === SUPPRIMER UN COMMENTAIRE ===
function deleteComment(commentId) {
    if (!confirm('Supprimer ce commentaire ?')) return;

    fetch('delete_comment.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({id: commentId})
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {
            // je retire le commentaire de la page
            document.getElementById('comment-' + commentId).remove();
        }
    });
}

// === PROTECTION XSS ===
// cette fonction empeche d'injecter du code HTML malveillant
function escapeHtml(text) {
    var div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// === MODE SOMBRE / CLAIR ===
var btnDark = document.getElementById('btn-dark-mode');
if (btnDark) {
    // je verifie si l'utilisateur avait choisi le mode clair avant
    if (localStorage.getItem('theme') == 'light') {
        document.body.classList.add('light-mode');
    }

    // quand je clique sur le bouton, je bascule le theme
    btnDark.addEventListener('click', function() {
        document.body.classList.toggle('light-mode');
        // je sauvegarde le choix dans le localStorage (ca reste meme si on ferme le navigateur)
        if (document.body.classList.contains('light-mode')) {
            localStorage.setItem('theme', 'light');
        } else {
            localStorage.setItem('theme', 'dark');
        }
    });
}
