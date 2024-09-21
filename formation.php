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

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Formations</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="admin_style.css">
</head>
<style>
    th {
        font-size: 16px;
        border-bottom: 3px solid #ffcb61;
        padding: 6px 20px;
        font-weight: 500;
    }

    td {
        font-weight: 400;
        padding: 6px 30px;
        font-size: 14px;
        text-align: center;
    }

    img {
        width: 20px;
        height: 20px;
        margin-right: 5px;
    }

    .add-icon img {
        width: 30px;
        height: 30px;
        margin-right: 5px;
        filter: invert(100%);
    }
    .table-wrapper {
    overflow-x: auto;
    max-width: 100%;
    }
    .more-info-link {
    display: inline-block;
    padding: 15px 25px;
    background-color: var(--main-color); /* Changement de couleur */
    color: var(--white); /* Changement de couleur */
    border-radius: 8px;
    text-decoration: none;
    transition: background-color 0.3s ease;
    margin-top: 20px;
    font-size: 1.2em;
}

.more-info-link:hover {
    background-color: #45a049;
}

/* Styles pour l'icône */
.more-info-icon {
    margin-right: 10px;
    font-size: 1.2em;
}

</style>

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
    <section>
        <h2 class="heading">Liste des Formations</h2>
        <a href='ajouter_formation.php' class='add-icon'> <img src='images/plus.png'> Ajouter</a>
        <div class="table-wrapper">
        <table class="accounts" border="2">
            <thead>
                <tr>
                    <th>ID de la formation</th>
                    <th>Nom</th>
                    <th>Prix</th>
                    <th>Description</th>
                    <th>Catégorie</th>
                    <th>Date de début</th>
                    <th>Date de fin</th>
                    <th>Lieu</th>
                    <th>Nombre de sessions</th>
                    <th>Formateur</th>
                    <th>Modifier</th>
                    <th>Supprimer</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Requête SQL pour récupérer les informations des formations
                $sql = "SELECT 
            products.id, 
            products.name, 
            products.price, 
            products.image, 
            products.Description, 
            formateur.Nom_formateur, 
            products.categorie, 
            session.date_debut, 
            session.date_fin, 
            session.Lieu, 
            COUNT(session.id_formation) AS Nombre_session
        FROM 
            products 
        INNER JOIN 
            formateur ON products.id_formateur = formateur.id_formateur
        LEFT JOIN 
            session ON products.id = session.id_formation
        GROUP BY 
            products.id;";
        
                $result = $connect->query($sql);
                // Vérifier si la requête s'est exécutée avec succès
                if ($result !== false) {
                    // Afficher les données dans le tableau
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row["id"] . "</td>";
                            echo "<td>" . $row["name"] . "</td>";
                            echo "<td>" . $row["price"] . "</td>";
                            echo "<td>" . $row["Description"] . "</td>";
                            echo "<td>" . $row["categorie"] . "</td>";
                            echo "<td>" . $row["date_debut"] . "</td>";
                            echo "<td>" . $row["date_fin"] . "</td>";
                            echo "<td>" . $row["Lieu"] . "</td>";
                            echo "<td>" . $row["Nombre_session"] . "</td>";
                            // Vous pouvez récupérer le nom du formateur à partir de la table des formateurs
                            echo "<td>" . $row["Nom_formateur"] . "</td>";
                            echo "<td><a href='modifier_formation.php?id=" . $row['id'] . "'><img src='images/pen.png' alt='Modifier'>Modifier</a></td>";
                            echo "<td><a href='supprimer_formation.php?delete=" . $row['id'] . "'><img src='images/trash.png' alt='Supprimer'> Supprimer</a></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8'>Aucune formation trouvée</td></tr>";
                    }
                } else {
                    // Gérer l'échec de la requête
                    echo "Erreur lors de l'exécution de la requête : " . $connect->error;
                }
                ?>
            </tbody>
        </table>  
        </div>
    </section>
    <div style="text-align: right;" >
        <a href="emploi.php"  class="more-info-link">
            <i class="fas fa-info-circle more-info-icon"></i>Information Plus
        </a>
    </div>

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

<?php
// Fermer la connexion à la base de données
mysqli_close($connect);
?>
