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

// Traitement de l'ajout d'une session de formation
if(isset($_POST['submit'])) {
    $nom_formation = $_POST['id'];
    $jour = $_POST['jour'];
    $date_debut = $_POST['date_debut'];
    $heure_debut = $_POST['heure_debut'];
    $heure_fin = $_POST['heure_fin'];
    $date_fin = $_POST['date_fin'];
    $lieu = $_POST['lieu'];

    $query_insert_session = "INSERT INTO session (id_formation, jour, date_debut, heure_debut, heure_fin, date_fin, Lieu) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert_session = $connect->prepare($query_insert_session);
    $stmt_insert_session->bind_param("issssss", $nom_formation, $jour, $date_debut, $heure_debut, $heure_fin, $date_fin, $lieu);
    if ($stmt_insert_session->execute()) {
        echo "<script>alert('Session de formation ajoutée avec succès.');</script>";
        
        // Rediriger vers la page des employés après l'ajout réussi
        header("Refresh: 5; URL=emploi.php");
    } else {
        echo "<script>alert('Erreur lors de l'ajout de la session de formation.');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une session de formation</title>
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
        <form action="" method="POST">
            <h3>Ajouter une session de formation</h3>
            <!-- Ajoutez les champs nécessaires pour ajouter une session -->
            <input type="number" name="id" maxlength="100" required placeholder="Entrez l'id de la formation" class="box">
            <select name="jour" required class="box">
                <option value="dimanche">Dimanche</option>
                <option value="lundi">Lundi</option>
                <option value="mardi">Mardi</option>
                <option value="mercredi">Mercredi</option>
                <option value="jeudi">Jeudi</option>
            </select>
            <input type="date" name="date_debut" required placeholder="Date de début" class="box">
            <input type="time" name="heure_debut" required placeholder="Heure de début" class="box">
            <input type="time" name="heure_fin" required placeholder="Heure de fin" class="box">
            <input type="date" name="date_fin" required placeholder="Date de fin" class="box">
            <input type="text" name="lieu" required placeholder="Lieu" class="box">

            <input type="submit" value="Ajouter maintenant" name="submit" class="btn">
        </form>
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
