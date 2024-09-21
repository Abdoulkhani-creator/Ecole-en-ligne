<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tutor";

$connect = mysqli_connect($servername, $username, $password, $dbname);

if (!$connect) {
    die("La connexion à la base de données a échoué : " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Nom_admin = mysqli_real_escape_string($connect, $_POST['Nom_admin']);
    $Prenom_admin = mysqli_real_escape_string($connect, $_POST['Prenom_admin']);
    $email = mysqli_real_escape_string($connect, $_POST['email']);
    $password = mysqli_real_escape_string($connect, $_POST['password']);
    $numero = mysqli_real_escape_string($connect, $_POST['numero']);

    // Vérifier si le mot de passe est bien confirmé
    if ($password != $_POST['password']) {
        echo "Les mots de passe ne correspondent pas.";
    } else {
        // Hacher le mot de passe
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Préparer la requête d'insertion
        $insert_query = "INSERT INTO `administrateur` (Nom_admin, Prenom_admin, email, password, numero) VALUES ('$Nom_admin', '$Prenom_admin', '$email', '$hashedPassword', '$numero')";

        // Exécuter la requête d'insertion
        $insert_result = mysqli_query($connect, $insert_query);

        // Vérifier si l'insertion s'est bien déroulée
        if ($insert_result) {
            // Rediriger vers la page admin_accounts.php après un court délai
            header("Refresh: 5; URL=admin_accounts.php");
            echo "Enregistrement réussi. Vous serez redirigé vers la page des administrateurs dans 5 secondes.";
        } else {
            echo "Erreur lors de l'enregistrement : " . mysqli_error($connect);
        }
    }
}

mysqli_close($connect);
?>
