<?php

include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:login.php');
   exit(); // Ajout pour arrêter l'exécution du script après la redirection
}

$result = mysqli_query($conn, "SELECT * FROM products");
if(isset($_POST['add_product'])){
   $formation = mysqli_real_escape_string($conn, $_POST['formation']);
   $description = mysqli_real_escape_string($conn, $_POST['description']);
   $image = $_FILES['image']['name'];
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img/'.$image;
   $cours = mysqli_real_escape_string($conn, $_POST['cours']); // Échapper les caractères spéciaux dans la description
   

   // Vérifier si le nom du produit existe déjà
   $select_product_name = mysqli_query($conn, "SELECT description FROM contenu WHERE  description = '$description'");
   if(mysqli_num_rows($select_product_name) > 0){
      $message[] = 'le contenu existe deja';
   } else {
      // Préparer et exécuter la requête d'insertion
      $add_product_query = mysqli_query($conn, "INSERT INTO contenu(id_formation, description, image, Cours)
                                                VALUES('$formation', '$description', '$image', '$cours')");
      
      if($add_product_query){
         if($image_size > 2000000){
            $message[] = 'Image size is too large';
         } else {
            // Déplacer le fichier image téléchargé vers le dossier spécifié
            if(move_uploaded_file($image_tmp_name, $image_folder)){
               $message[] = 'le contenu est bien rempli!';
            } else {
               $message[] = 'Failed to move uploaded file';
            }
         }
      } else {
         // Afficher le message d'erreur de MySQL en cas d'échec de la requête
         $message[] = 'Query failed: ' . mysqli_error($conn);
      }
   }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>products</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom admin css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>
   
<?php include 'admin_header.php'; ?>

<!-- product CRUD section starts  -->

<section class="add-products">

   <h1 class="title">Formation</h1>

   <form action="" method="post" enctype="multipart/form-data">
      <h3>Remplir Formation</h3>
      <input type="hidden" name="formation" value="<?php echo isset($_GET['formation_id']) ? $_GET['formation_id'] : ''; ?>">

      <textarea name="description" class="box" placeholder="Entrer la description de la formation" id="" cols="30" rows="10"></textarea>
      <input type="file" name="image" accept="image/jpg, image/jpeg, image/png, .pdf, .doc, .docx, .txt" class="box" required>
      <textarea name="cours" class="box" placeholder="Entrer le cours" id="" cols="30" rows="10"></textarea>
      <input type="submit" value="Remplir contenu" name="add_product" class="btn">
   </form>

</section>

<!-- product CRUD section ends -->

<!-- show products  -->










<!-- custom admin js file link  -->
<script src="js/admin_script.js"></script>

</body>
</html>