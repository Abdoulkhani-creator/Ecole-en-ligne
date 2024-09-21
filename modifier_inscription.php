<?php
session_start();

$admin_id = $_SESSION['admin_id'] ?? null;

// Vérifier si l'ID de l'administrateur est présent dans la session
if (!$admin_id) {
    // Rediriger vers la page de connexion si l'ID n'est pas présent
    header('location: connexion.php');
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tutor";

// Établir une connexion à la base de données
$connect = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion à la base de données
if ($connect->connect_error) {
    die("La connexion à la base de données a échoué : " . $connect->connect_error);
}

// Récupérer l'ID de l'inscription à partir de l'URL
$id_inscription = $_GET['id'] ?? null;

// Vérifier si l'ID de l'inscription est défini
if ($id_inscription) {
    // Préparer la requête SQL pour récupérer les informations de l'inscription
    $query_select_inscription = "SELECT * FROM inscription WHERE id_inscription = ?";
    
    // Préparer l'instruction SQL
    $stmt_select_inscription = $connect->prepare($query_select_inscription);
    
    // Lier les paramètres
    $stmt_select_inscription->bind_param("i", $id_inscription); // i pour indiquer que c'est un Integer
    
    // Exécuter la requête
    $stmt_select_inscription->execute();
    
    // Récupérer le résultat de la requête
    $result_select_inscription = $stmt_select_inscription->get_result();
    
    // Vérifier s'il y a des résultats
    if ($result_select_inscription->num_rows > 0) {
        // Récupérer les données de l'inscription
        $inscription_data = $result_select_inscription->fetch_assoc();
        
        // Stocker les données dans des variables pour les afficher dans le formulaire
        $inscription_nom = $inscription_data['Nom'];
        $inscription_prenom = $inscription_data['Prenom'];
        $inscription_email = $inscription_data['Email'];
        $inscription_password = $inscription_data['password'];
        $inscription_status = $inscription_data['Status'];
        $id_formation = $inscription_data['id_formation'];
        $date_inscription = $inscription_data['date_inscription'];
    } else {
        // Si aucune inscription n'est trouvée avec l'ID spécifié, afficher un message d'erreur
        echo "Aucune inscription trouvée avec cet identifiant.";
        exit();
    }
} else {
    // Si l'ID de l'inscription n'est pas défini dans l'URL, afficher un message d'erreur
    echo "L'identifiant de l'inscription n'est pas spécifié.";
    exit();
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une inscription</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="admin_style.css">
</head>

<body>
    <!-- Entête de la page -->
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
                <!-- Affichage du profil de l'administrateur -->
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

    <!-- Formulaire de modification de l'inscription -->
    <section class="form-container">
        <form action="modifier_inscription1.php" method="POST">
            <h3>Modifier une inscription</h3>
            <input type="hidden" name="id_inscription" value="<?php echo $id_inscription; ?>">
            <input type="text" name="Nom" maxlength="100" required placeholder="Nom" class="box" value="<?php echo $inscription_nom; ?>">
            <input type="text" name="Prenom" maxlength="100" required placeholder="Prénom" class="box" value="<?php echo $inscription_prenom; ?>">
            <input type="email" name="Email" maxlength="100" required placeholder="Adresse email" class="box" value="<?php echo $inscription_email; ?>">
            <input type="password" name="password" maxlength="100" required placeholder="Mot de passe" class="box" value="<?php echo $inscription_password; ?>">
            <select name="Status" required class="box">
                <option value="participant" <?php if ($inscription_status === 'participant') echo 'selected'; ?>>Participant</option>
                <option value="inscrit" <?php if ($inscription_status === 'inscrit') echo 'selected'; ?>>Inscrit</option>
            </select>
            <input type="number" name="id_formation" placeholder="ID Formation" class="box" value="<?php echo $id_formation; ?>">
            <input type="date" name="date_inscription" placeholder="Date d'inscription" class="box" value="<?php echo $date_inscription; ?>">
            <input type="submit" value="Modifier maintenant" name="submit" class="btn">
        </form>
    </section>

    <!-- Script JavaScript pour la barre de navigation -->
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

