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

// Récupérer l'ID du formateur à partir de l'URL
$formateur_id = $_GET['id'] ?? null;

// Vérifier si l'ID du formateur est défini
if ($formateur_id) {
    // Préparer la requête SQL pour récupérer les informations du formateur
    $query_select_formateur = "SELECT * FROM formateur WHERE id_formateur = ?";
    
    // Préparer l'instruction SQL
    $stmt_select_formateur = $connect->prepare($query_select_formateur);
    
    // Lier les paramètres
    $stmt_select_formateur->bind_param("i", $formateur_id); // i pour indiquer que c'est un Integer
    
    // Exécuter la requête
    $stmt_select_formateur->execute();
    
    // Récupérer le résultat de la requête
    $result_select_formateur = $stmt_select_formateur->get_result();
    
    // Vérifier s'il y a des résultats
    if ($result_select_formateur->num_rows > 0) {
        // Récupérer les données du formateur
        $formateur_data = $result_select_formateur->fetch_assoc();
        
        // Stocker les données dans des variables pour les afficher dans le formulaire
        $formateur_nom = $formateur_data['Nom_formateur'];
        $formateur_prenom = $formateur_data['Prenom_formateur'];
        $formateur_email = $formateur_data['email'];
        $formateur_password = $formateur_data['password'];
        $formateur_numero = $formateur_data['numero'];
    } else {
        // Si aucun formateur trouvé avec l'ID spécifié, rediriger vers une page d'erreur ou afficher un message approprié
        echo "Aucun formateur trouvé avec cet identifiant.";
        exit();
    }
} else {
    // Si l'ID du formateur n'est pas défini dans l'URL, rediriger vers une page d'erreur ou afficher un message approprié
    echo "L'identifiant du formateur n'est pas spécifié.";
    exit();
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier formateur</title>
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
    <!-- Section de modification des formateurs -->
    <section class="add-products">
        <form action="modifier_formateur1.php" method="POST">
            <h3>Modifier un formateur</h3>
            <input type="hidden" name="id_formateur" value="<?php echo $formateur_id; ?>">
            <input type="text" name="nom_formateur" maxlength="100" required placeholder="Nom du formateur" class="box" value="<?php echo $formateur_nom; ?>">
            <input type="text" name="prenom_formateur" maxlength="100" required placeholder="Prénom du formateur" class="box" value="<?php echo $formateur_prenom; ?>">
            <input type="email" name="email" maxlength="100" required placeholder="Email du formateur" class="box" value="<?php echo $formateur_email; ?>">
            <input type="password" name="password" maxlength="100" required placeholder="Mot de passe du formateur" class="box" value="<?php echo $formateur_password; ?>">
            <input type="number" name="numero" maxlength="20" required placeholder="Numéro du formateur" class="box" value="<?php echo $formateur_numero; ?>">
            <input type="submit" value="Modifier maintenant" name="modifier_formateur" class="btn">
        </form>
    </section>

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
