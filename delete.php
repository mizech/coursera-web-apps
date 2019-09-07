<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Michael Zech</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
    <link rel="stylesheet" href="./css/style.css" type="text/css">
  </head>
  <body>
    <div class="container">
      <h1>Deleteing Profile</h1>
      <?php     
          session_start();
          
          if (!isset($_SESSION["user_id"])) {
              die("ACCESS DENIED");
          }

        $id = $_GET["profile_id"];
        require_once "connect.php";

        $stmt = $pdo->prepare(
            'SELECT first_name, last_name FROM profile WHERE profile_id = ' . $id); 
        $stmt->execute();
        
        $columns = $stmt->fetchAll();
        $firstName = $columns[0]["first_name"];
        $lastName = $columns[0]["last_name"];
        ?>
        <div>
          <p>First Name: <?php echo $firstName; ?></p>
          <p>Last Name: <?php echo $lastName; ?></p>
        </div>
        <form method="POST" action="#">
            <input type="submit" value="Delete" name="delete" class="btn btn-danger" />
        </form>
        <div class="nav-element"><a id="cancel-delete" href="index.php">Cancel</a></div>
        <?php
            if (isset($_POST["delete"])) {
                $sql = 'DELETE FROM Profile WHERE profile_id = :profile_id';
                $stmt = $pdo->prepare($sql);
        
                $stmt->execute([":profile_id" => $id]);

                $sql = 'DELETE FROM Education WHERE profile_id = :profile_id';
                $stmt = $pdo->prepare($sql);
        
                $stmt->execute([":profile_id" => $id]);
        
                $_SESSION['delete_confirmed'] = true;
                
                header("location: index.php");
                return;
            }
        ?>
    </div>
  </body>
</html>