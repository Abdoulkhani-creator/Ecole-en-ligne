<?php
session_start();

$admin_id = $_SESSION['admin_id'] ?? null;

// Si l'ID de l'administrateur n'est pas présent dans la session, rediriger vers la page de connexion
if (!$admin_id) {
    header('location: connexion.php');
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tutor";

$connect = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion à la base de données
if ($connect->connect_error) {
    die("La connexion à la base de données a échoué : " . $connect->connect_error);
}

// Récupérer le profil de l'administrateur
$query_profile = "SELECT * FROM administrateur WHERE Nom_admin = ?";
$stmt_profile = $connect->prepare($query_profile);
$stmt_profile->bind_param("s", $admin_id);
$stmt_profile->execute();
$result_profile = $stmt_profile->get_result();

// Vérifier si la requête s'est exécutée avec succès
if ($result_profile && $result_profile->num_rows > 0) {
    $fetch_profile = $result_profile->fetch_assoc();
    $nom_directeur = $fetch_profile['Nom_admin'];
} else {
    // Gérer l'échec de la requête
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une formation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="admin_style.css">
</head>

<body>
    <header class="header">
        <section class="flex">
            <a href="Accueil.php" class="logo">Admin<span>Panel</span></a>
            <nav class="navbar">
                <a href="Accueil.php">Accueil</a>
                <a href="admin_accounts.php">Admins</a>
                <a href="formation.php">Formation</a>
                <a href="inscription.php">Inscription</a>
                <a href="formateur.php">Formateur</a>
            </nav>
            <div class="icons">
                <div id="menu-btn" class="fas fa-bars"></div>
                <div id="user-btn" class="fas fa-user"></div>
            </div>
            <div class="profile">
                <?php
                if ($result_profile && $result_profile->num_rows > 0) {
                ?>
                    <p><?= $nom_directeur; ?></p>
                    <a href="update_profile.php" class="btn">Mettre à jour le profil</a>
                    <div class="flex-btn">
                        <a href="connexion.php" class="option-btn">Connexion</a>
                        <a href="Enregister_admin.php" class="option-btn">Enregistrer</a>
                    </div>
                    <a href="components/admin_logout.php" onclick="return confirm('Déconnexion de ce site ?');" class="delete-btn">Déconnexion</a>
                <?php
                } else {
                    echo '<p class="empty">Aucun profil disponible</p>';
                }
                ?>
            </div>
        </section>
    </header>

    <section class="form-container">
    <form action="ajouter_formation1.php" method="POST" enctype="multipart/form-data">
        <h3>Ajouter une nouvelle formation</h3>
        <!-- Ajoutez les champs nécessaires pour ajouter une formation -->
        <input type="number" name="id_formation" maxlength="100" required placeholder="ID de la formation" class="box">
        <input type="number" name="id_formateur" maxlength="100" required placeholder="ID du formateur" class="box">
        <select name="type_formation" required class="box" id="selectTypeFormation">
        <option value="En ligne">En ligne</option>
        <option value="Présentielle">Présentielle</option>
        </select>

       <div id="divNombreChapitres" style="display: none;">
         <input type="number" name="nombre_chapitres" placeholder="Nombre de chapitres" class="box">
          <div id="chapitres"></div>
        </div>
        <input type="text" name="nom_formation" maxlength="100" required placeholder="Nom de la formation" class="box">
        <input type="file" name="formation_image" accept="image/*" required class="box">
        <textarea name="description" maxlength="500" required placeholder="Description de la formation" class="box"></textarea>
        <select name="categorie" required class="box" id="selectCategorie">
            <option value="Gratuite">Gratuite</option>
            <option value="Payante">Payante</option>
        </select>
        <div id="divPrix" style="display: none;">
            <input type="number" name="prix"  placeholder="Prix de la formation" class="box">
        </div>
        
        <input type="submit" value="Ajouter maintenant" name="submit" class="btn">
    </form>
</section>
<script>
    // Récupérer le selecteur de catégorie et le champ du prix
    const selectCategorie = document.getElementById('selectCategorie');
    const divPrix = document.getElementById('divPrix');

    // Ajouter un écouteur d'événement de changement sur le selecteur de catégorie
    selectCategorie.addEventListener('change', function() {
        // Vérifier si la catégorie sélectionnée est 'Payante'
        if (selectCategorie.value === 'Payante') {
            // Afficher le champ du prix
            divPrix.style.display = 'block';
        } else {
            // Cacher le champ du prix
            divPrix.style.display = 'none';
        }
    });
</script>
    <script>
        let navbar = document.querySelector('.header .flex .navbar');
        let profile = document.querySelector('.header .flex .profile');

        document.querySelector('#user-btn').onclick = () => {
            profile.classList.toggle('active');
            navbar.classList.remove('active');
        }

        document.querySelector('#menu-btn').onclick = () => {
            navbar.classList.toggle('active');
            profile.classList.remove('active');
        }

        window.onscroll = () => {
            profile.classList.remove('active');
            navbar.classList.remove('active');
        }
    </script>
<script>
    // Récupérer le selecteur de type de formation et le champ du nombre de chapitres
    const selectTypeFormation = document.getElementById('selectTypeFormation');
    const divNombreChapitres = document.getElementById('divNombreChapitres');

    // Ajouter un écouteur d'événement de changement sur le selecteur de type de formation
    selectTypeFormation.addEventListener('change', function() {
        // Vérifier si le type de formation sélectionné est 'Présentielle'
        if (selectTypeFormation.value === 'Présentielle') {
            // Afficher le champ du nombre de chapitres
            divNombreChapitres.style.display = 'block';
            afficherChampsChapitres();
        } else {
            // Cacher le champ du nombre de chapitres
            divNombreChapitres.style.display = 'none';
        }
    });

    // Fonction pour afficher les champs de saisie du nom des chapitres
    function afficherChampsChapitres() {
        const divChapitres = document.getElementById('chapitres');
        divChapitres.innerHTML = '';

        const nombreChapitres = parseInt(document.getElementsByName('nombre_chapitres')[0].value);

        for (let i = 1; i <= nombreChapitres; i++) {
            const inputChapitre = document.createElement('input');
            inputChapitre.type = 'text';
            inputChapitre.name = 'chapitre_' + i;
            inputChapitre.placeholder = 'Nom du chapitre ' + i;
            inputChapitre.required = true;
            inputChapitre.classList.add('box');
            divChapitres.appendChild(inputChapitre);
        }
    }

    // Ajouter un écouteur d'événement de saisie sur le champ du nombre de chapitres
    document.getElementsByName('nombre_chapitres')[0].addEventListener('input', function() {
        afficherChampsChapitres();
    });
</script>
</body>

</html>