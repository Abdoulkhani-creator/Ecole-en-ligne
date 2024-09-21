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

// Récupérer les sessions existantes avec le nom de la formation
$query_sessions = "SELECT s.id_formation, f.name AS nom_formation, s.jour, s.date_debut,s.heure_debut, s.heure_fin,s.Lieu,s.date_fin 
                    FROM session s
                    LEFT JOIN products f ON s.id_formation = f.id";
$result_sessions = $connect->query($query_sessions);

// Initialiser les variables pour stocker les valeurs existantes
$nom_formation_existante = "";
$jour_existant = "";
$heure_debut_existante = "";
$heure_fin_existante = "";
$date_debut_existante = "";
$date_fin_existante = "";
$lieu_existant = "";

// Vérifier si des sessions existent
if ($result_sessions && $result_sessions->num_rows > 0) {
    // Récupérer la première session trouvée (vous pouvez ajuster la logique selon vos besoins)
    $row = $result_sessions->fetch_assoc();
    $nom_formation_existante = $row["nom_formation"];
    $jour_existant = $row["jour"];
    $date_debut_existante = $row["date_debut"];
    $heure_debut_existante = date("H:i", strtotime($row["heure_debut"])); // Convertir l'heure de début au format HH:MM
    $heure_fin_existante = date("H:i", strtotime($row["heure_fin"])); // Convertir l'heure de fin au format HH:MM
    $date_fin_existante = $row["date_fin"];
    $lieu_existant = $row["Lieu"];
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
        <form action="modifier_emploi1.php" method="POST">
            <h3>Modifier une session de formation</h3>
            <!-- Ajoutez les champs nécessaires pour ajouter une session -->
            <input type="text" name="nom_formation" maxlength="100" required placeholder="Nom de la formation" class="box" value="<?php echo $nom_formation_existante; ?>">
            <select name="jour" required class="box">
                <option value="dimanche" <?php if($jour_existant == "dimanche") echo "selected"; ?>>Dimanche</option>
                <option value="lundi" <?php if($jour_existant == "lundi") echo "selected"; ?>>Lundi</option>
                <option value="mardi" <?php if($jour_existant == "mardi") echo "selected"; ?>>Mardi</option>
                <option value="mercredi" <?php if($jour_existant == "mercredi") echo "selected"; ?>>Mercredi</option>
                <option value="jeudi" <?php if($jour_existant == "jeudi") echo "selected"; ?>>Jeudi</option>
            </select>
            <input type="date" name="date_debut" required placeholder="Date de début" class="box" value="<?php echo $date_debut_existante; ?>">
            <input type="time" name="heure_debut" required placeholder="Heure de début" class="box" value="<?php echo $heure_debut_existante; ?>">
            <input type="time" name="heure_fin" required placeholder="Heure de fin" class="box" value="<?php echo $heure_fin_existante; ?>">
            <input type="date" name="date_fin" required placeholder="Date de fin" class="box" value="<?php echo $date_fin_existante; ?>">
            <input type="text" name="lieu" required placeholder="Lieu" class="box" value="<?php echo $lieu_existant; ?>">

            <input type="submit" value="Modifier maintenant" name="submit" class="btn">
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
