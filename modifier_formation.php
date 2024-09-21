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

// Récupérer l'ID de la formation à partir de l'URL
$formation_id = $_GET['id'] ?? null;

// Vérifier si l'ID de la formation est défini
if ($formation_id) {
    // Préparer la requête SQL pour récupérer les informations de la formation
    $query_select_formation = "SELECT f.*FROM products f  WHERE f.id = ?";
    // Préparer l'instruction SQL
    $stmt_select_formation = $connect->prepare($query_select_formation);
    
    // Lier les paramètres
    $stmt_select_formation->bind_param("i", $formation_id); // i pour indiquer que c'est un Integer
    
    // Exécuter la requête
    $stmt_select_formation->execute();
    
    // Récupérer le résultat de la requête
    $result_select_formation = $stmt_select_formation->get_result();
    
    // Vérifier s'il y a des résultats
    if ($result_select_formation->num_rows > 0) {
        // Récupérer les données de la formation
        $formation_data = $result_select_formation->fetch_assoc();
        // Récupérer les chapitres de la formation
        $query_select_chapitres = "SELECT * FROM Chapitre WHERE id_formation = ?";
        // Préparer l'instruction SQL
        $stmt_select_chapitres = $connect->prepare($query_select_chapitres);
        // Lier les paramètres
        $stmt_select_chapitres->bind_param("i", $formation_id);
        // Exécuter la requête
        $stmt_select_chapitres->execute();
        // Récupérer le résultat de la requête
        $result_select_chapitres = $stmt_select_chapitres->get_result();
        // Récupérer le nombre de chapitres
        $nombre_chapitres = $result_select_chapitres->num_rows;
        // Récupérer les données des chapitres
        $chapitres_data = $result_select_chapitres->fetch_all(MYSQLI_ASSOC);
    
    } else {
        // Si aucune formation trouvée avec l'ID spécifié, rediriger vers une page d'erreur ou afficher un message approprié
        echo "Aucune formation trouvée avec cet identifiant.";
        exit();
    }
} else {
    // Si l'ID de la formation n'est pas défini dans l'URL, rediriger vers une page d'erreur ou afficher un message approprié
    echo "L'identifiant de la formation n'est pas spécifié.";
    exit();
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier formation</title>
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
                <a href="inscription.php">inscription</a>
                <a href="formateur.php">Formateur</a>
            </nav>
            <div class="icons">
                <div id="menu-btn" class="fas fa-bars"></div>
                <div id="user-btn" class="fas fa-user"></div>
            </div>
            <div class="profile">
                <?php
                $query_profile = "SELECT * FROM `administrateur` WHERE Nom_admin = ?";
                $stmt_profile = $connect->prepare($query_profile);
                $stmt_profile->bind_param("s", $admin_id); // s pour indiquer que c'est un String
                $stmt_profile->execute();
                $result_profile = $stmt_profile->get_result();

                if ($result_profile && $result_profile->num_rows > 0) {
                    $fetch_profile = $result_profile->fetch_assoc();
                ?>
                    <p><?= $fetch_profile['Nom_admin']; ?></p>
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
    <form action="modifier_formation1.php" method="POST" enctype="multipart/form-data">
        <h3>Modifier formation</h3>
        <!-- Affichage des informations de la formation -->
        <input type="hidden" name="id_formation" value="<?= $formation_data['id']; ?>">
        <input type="number" name="id_formateur" value="<?= $formation_data['id_formateur']; ?>" maxlength="100" required placeholder="ID du formateur" class="box">
        <input type="text" name="formation_nom" value="<?= $formation_data['name']; ?>" maxlength="100" required placeholder="Nom de la formation" class="box">
        <input type="file" name="image" accept="image/*" required class="box">
        <textarea name="description" maxlength="500" required placeholder="Description de la formation" class="box"><?= $formation_data['Description']; ?></textarea>
        <select name="categorie" required class="box" id="selectCategorie">
            <option value="Gratuite" <?= ($formation_data['categorie'] == 'Gratuite') ? 'selected' : ''; ?>>Gratuite</option>
            <option value="Payante" <?= ($formation_data['categorie'] == 'Payante') ? 'selected' : ''; ?>>Payante</option>
        </select>
        <div id="divPrix" style="<?= ($formation_data['categorie'] == 'Payante') ? 'display: block;' : 'display: none;'; ?>">
            <input type="number" name="prix" value="<?= $formation_data['price']; ?>" required placeholder="Prix de la formation" class="box">
        </div>
        
        <input type="hidden" name="nombre_chapitres" value="<?= $nombre_chapitres; ?>">
        <?php for ($i = 1; $i <= $nombre_chapitres; $i++) : ?>
          <label for="chapitre_<?= $i; ?>">Chapitre <?= $i; ?> :</label>
          <input type="hidden" name="chapitre_id_<?= $i; ?>" value="<?= $chapitres_data[$i - 1]['id_chapitre']; ?>">
          <input type="text" name="chapitre_<?= $i; ?>" value="<?= $chapitres_data[$i - 1]['nom_chapitre']; ?>" required class="box">
        <?php endfor; ?>
        <input type="submit" value="Modifier maintenant" name="modifier_formation" class="btn">
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
    <!-- Script pour la gestion de la barre de navigation et du profil utilisateur -->
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
</body>

</html>
