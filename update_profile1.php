<?php
// Assurez-vous de démarrer la session si ce n'est pas déjà fait
session_start();

// Incluez le fichier de configuration de la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tutor";

$connect = mysqli_connect($servername, $username, $password, $dbname);

if (!$connect) {
    die("La connexion à la base de données a échoué : " . mysqli_connect_error());
}

// Vérifiez si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérez les données du formulaire
    $id_directeur = $_POST['id_directeur'];
    $new_nom = $_POST['new_nom'];
    $new_pass = $_POST['new_pass'];
    $new_prenom = $_POST['new_prenom'];
    $new_email = $_POST['new_email'];
    $new_numero = $_POST['new_numero'];

    // Vérifiez si l'ID du directeur existe
    $check_directeur_query = "SELECT * FROM administrateur WHERE id_admin = $id_directeur";
    $check_directeur_result = mysqli_query($connect, $check_directeur_query);

    if (mysqli_num_rows($check_directeur_result) > 0) {
        // Mettez à jour toutes les colonnes du profil dans la base de données     
        // Utilisation de password_hash pour hacher le mot de passe
        $hashedPassword = password_hash($new_pass, PASSWORD_DEFAULT);
        $update_query = "UPDATE administrateur SET Nom_admin = '$new_nom', password = '$hashedPassword', Prenom_admin = '$new_prenom', email = '$new_email', numero = '$new_numero' WHERE id_admin = $id_directeur";
        $update_result = mysqli_query($connect, $update_query);

        if ($update_result) {
            // Rediriger vers une autre page après un court délai
            header("Refresh: 5; URL=admin_accounts.php");
            echo "Profil mis à jour avec succès. Vous serez redirigé vers la page des administrateurs dans 5 secondes.";
        } else {
            echo "Erreur lors de la mise à jour du profil : " . mysqli_error($connect);
        }
    } else {
        echo "ID du directeur non trouvé.";
    }
}

// Fermez la connexion à la base de données
mysqli_close($connect);
?>
