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
    <title>Comptes Formateurs</title>
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

    <section class="accounts">

        <h1 class="heading">Comptes Formateurs</h1>

        <div class="box-container">

            <div class="box">
                <p>Ajouter un nouvel formateur</p>
                <a href="ajouter_formateur.php" class="option-btn">Ajouter</a>
            </div>
            <?php
            $username = "root";
            $servername = "localhost";
            $dbname = "tutor";
            $password = "";

            $connect = mysqli_connect($servername, $username, $password, $dbname);

            if (!$connect) {
                die("La connexion à la base de données a échoué : " . mysqli_connect_error());
            }

            $select_account = mysqli_query($connect, "SELECT id_formateur, Nom_formateur, Prenom_formateur FROM formateur");

            if ($select_account && mysqli_num_rows($select_account) > 0) {
                while ($fetch_accounts = mysqli_fetch_assoc($select_account)) {
            ?>
                    <div class="box">
                        <p>ID du formateur : <span><?= $fetch_accounts['id_formateur']; ?></span></p>
                        <p>Nom du formateur : <span><?= $fetch_accounts['Nom_formateur']; ?></span></p>
                        <p>Prénom du formateur : <span><?= $fetch_accounts['Prenom_formateur']; ?></span></p>
                        <div class="flex-btn">
                            <a href="modifier_formateur.php?id=<?= $fetch_accounts['id_formateur']; ?>" class="option-btn">Modifier</a>
                            <a href="supprimer_formateur.php?delete=<?= $fetch_accounts['id_formateur']; ?>" class="delete-btn" onclick="return confirm('Supprimer ce compte ?');">Supprimer</a>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo '<p class="empty">Aucun compte disponible</p>';
            }

            mysqli_close($connect);
            ?>


        </div>

    </section>
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
