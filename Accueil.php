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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord</title>
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
                <a href="formation.php">formation</a>
                <a href="inscription.php">inscription</a>
                <a href="formateur.php">formateur</a>
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

    <section class="dashboard">
        <h1 class="heading">Tableau de bord</h1>

        <div class="box-container">
            <div class="box">
                <h3>Bienvenue!</h3>
                <p><?= isset($fetch_profile['Nom_admin']) ? $fetch_profile['Nom_admin'] : "Nom non disponible"; ?></p>
                <a href="update_profile.php" class="btn">Mettre à jour le profil</a>
            </div>
            <div class="box">
                <?php
                $query_orders = "SELECT * FROM `products`";
                $result_orders = mysqli_query($connect, $query_orders);
                $numbers_of_orders = mysqli_num_rows($result_orders);
                ?>
                <h3><?= $numbers_of_orders; ?></h3>
                <p>Total des formations</p>
                <a href="formation.php" class="btn">Formation</a>
            </div>
            <!-- Pour les employés -->
            <?php
            $query_employes = "SELECT COUNT(*) AS total_employes FROM `formateur`";
            $result_employes = mysqli_query($connect, $query_employes);
            $row_employes = mysqli_fetch_assoc($result_employes);
            $total_employes = $row_employes['total_employes'];
            ?>
            <div class="box">
                <h3><?= $total_employes; ?></h3>
                <p>Total des formateurs</p>
                <a href="formateur.php" class="btn">voir les formateurs</a>
            </div>
            <!-- Section pour le nombre total de plats -->
            <?php
            $query_inscriptions = "SELECT COUNT(*) AS total_inscriptions FROM `inscription`";
            $result_inscriptions = mysqli_query($connect, $query_inscriptions);
            $row_inscriptions = mysqli_fetch_assoc($result_inscriptions);
            $total_inscriptions = $row_inscriptions['total_inscriptions'];
            ?>
            <div class="box">
                <h3><?= $total_inscriptions; ?></h3>
                <p>Total des inscriptions</p>
                <a href="inscription.php" class="btn">voir les inscriptions</a>
            </div>
            <!-- Pour la modification d'un plat -->
            <div class="box">
                <h3>ajouter formation</h3>
                <p>Ajouter une formation</p>
                <a href="ajouter_formation.php" class="btn">Ajouter une formation</a>
            </div>
            <!-- Pour l'ajout d'un employé -->
            <div class="box">
                <h3>Ajouter formateur</h3>
                <p>Ajouter un formateur</p>
                <a href="ajouter_formateur.php" class="btn">Ajouter un formateur</a>
            </div>
            <?php
            $query_admins = "SELECT COUNT(*) AS total_admins FROM `administrateur`";
            $result_admins = mysqli_query($connect, $query_admins);
            $row_admins = mysqli_fetch_assoc($result_admins);?>
            <script>
    let navbar = document.querySelector('.header .flex .navbar');
    let profile = document.querySelector('.header .flex .profile');
    let formContainer = document.querySelector('.form-container');

    document.querySelector('#user-btn').onclick = () => {
        profile.classList.toggle('active');
        navbar.classList.remove('active');
    }

    document.querySelector('#menu-btn').onclick = () => {
        navbar.classList.toggle('active');
        profile.classList.remove('active');
    }

    // Ajout de la gestion du clic en dehors du formulaire pour fermer le menu
    formContainer.onclick = () => {
        navbar.classList.remove('active');
        profile.classList.remove('active');
    };

    window.onscroll = () => {
        profile.classList.remove('active');
        navbar.classList.remove('active');
    }
</script>

