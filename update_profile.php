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
    <title>Modifier le Profil</title>
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

<!-- Section de mise à jour du profil de l'administrateur -->
<section class="add-products">
   <form action="update_profile1.php" method="POST">
      <h3>Modifier le Profil</h3>
      <label>ID du Directeur:</label>
      <input type="text" name="id_directeur" placeholder="ID du Directeur" class="box" required value="<?php echo $fetch_profile['id_admin']; ?>">
      <label>Nouveau Nom:</label>
      <input type="text" name="new_nom" maxlength="20" placeholder="Nouveau Nom" class="box" required value="<?php echo $fetch_profile['Nom_admin']; ?>">
      <label>Nouveau Prénom:</label>
      <input type="text" name="new_prenom" maxlength="20" placeholder="Nouveau Prénom" class="box" required value="<?php echo $fetch_profile['Prenom_admin']; ?>">
      <label>Nouveau Email:</label>
      <input type="email" name="new_email" maxlength="100" placeholder="Nouveau Email" class="box" required value="<?php echo $fetch_profile['email']; ?>">
      <label>Nouveau Mot de Passe:</label>
      <input type="number" name="new_numero" maxlength="100" placeholder="Nouveau Numero" class="box" required value="<?php echo $fetch_profile['numero']; ?>">
      <label>Nouveau Numero:</label>
      <input type="password" name="new_pass" maxlength="20" placeholder="Nouveau Mot de Passe" class="box" required value="<?php echo $fetch_profile['password']; ?>">
      <input type="submit" value="Modifier le Profil" name="submit" class="btn">
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
