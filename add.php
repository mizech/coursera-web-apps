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
      <?php   
          session_start();

          require_once "./utils.php";
          
          if (!isset($_SESSION["user_id"])) {
              die("ACCESS DENIED");
          }

          if (isset($_SESSION["error"])) {
            echo "<p class='error'>" . $_SESSION['error'] . "</p>";
            unset($_SESSION['error']);
          }
      ?>
      <h1>Adding Profile for <?php echo $_SESSION["name"]; ?></h1>
      <form method="POST" action="#">
        <div class="form-group">
            <label for="first_name">First Name: </label>
            <input type="text" class="form-control" name="first_name" id="first_name" >
        </div>
        <div class="form-group">
            <label for="last_name">Last Name: </label>
            <input type="text" class="form-control" name="last_name" id="last_name" >
        </div>
        <div class="form-group">
            <label for="email">Email: </label>
            <input type="text" class="form-control" name="email" id="email" >
        </div>
        <div class="form-group">
            <label for="headline">Headline: </label>
            <input type="text" class="form-control" name="headline" id="headline" >
        </div>
        <div class="form-group">
            <label for="summary">Summary: </label>
            <textarea class="form-control" name="summary" id="summary" rows="8" cols="80"></textarea>
        </div>
        <div class="form-group">
            <label for="summary">Position: </label>
            <button name="add-position" id="add-position" class="btn btn-primary">+</button>
        </div>
        <div id="positions"></div>
        <button type="submit" name="add" class="btn btn-primary">Add</button>
        <button type="submit" name="cancel" class="btn btn-warning">Cancel</button>
      </form>

      <?php
        if (isset($_POST["cancel"])) {
          header("location: index.php");
          return;
        }
          
        if (isset($_POST["add"])) {
          $firstName = htmlentities($_POST["first_name"]);
          $lastName = htmlentities($_POST["last_name"]);
          $email = htmlentities($_POST["email"]);
          $headline = htmlentities($_POST["headline"]);
          $summary = htmlentities($_POST["summary"]);

          $validatePosResult = validatePos();

          if (strlen($validatePosResult) > 0) {
            $_SESSION["error"] = $validatePosResult;
            header("Location: add.php");
            return false;
          }

          if (isset($_POST["add"])
              && ($firstName == "" || $lastName == "" || $email == "" || $headline == "" || $summary == "")) {
            $_SESSION['error'] = "All fields are required";
            header("Location: add.php");
            return;
          } else if (strpos($email, "@") === false) {
            $_SESSION['error'] = "Email address must contain @";
            header("Location: add.php");
            return;
          } else if (isset($_POST["add"])) {
              require_once "connect.php";

              $sql
                  = "INSERT INTO Profile (user_id, first_name, last_name, email, headline, summary) 
                      VALUES (:id, :fn, :ln, :em, :hl, :su)";
              require_once "connect.php";

              $stmt = $pdo->prepare($sql);
              $stmt->execute(
                  array(
                      ":id" => $_SESSION["user_id"], 
                      ":fn" => $firstName, 
                      ":ln" => $lastName, 
                      ":em" => $email, 
                      ":hl" => $headline,
                      ":su" => $summary));

                $lastId = $pdo->lastInsertId(); 

                $i = 1;
                $rank = 1;
      
                while ($i < 10) {
                  if (isset($_POST["desc" . $i])) {
                    $desc = htmlentities($_POST["desc" . $i]);
                    $year = htmlentities($_POST["year" . $i]);
      
                    $sql = "INSERT INTO Position (profile_id, rank, year, description) VALUES (:lid, :rank, :ye, :de)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute(array(":lid" => $lastId, ":rank" => $rank++, ":ye" => $year, ":de" => $desc));
                  }
      
                  $i++;
                }
              
              $_SESSION["added_profile"] = true;
              header("location: index.php");
          } 
        }
      ?>
    </div>
    <script src="js/jquery.js"></script>
    <script src="js/add.js"></script>
  </body>
</html>