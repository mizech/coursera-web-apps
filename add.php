<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Michael Zech</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css">
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
            echo "<p class='error' style='color: red;'>" . $_SESSION['error'] . "</p>";
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
            <label for="summary">Education: </label>
            <button name="add-education" id="add-education" class="btn btn-primary">+</button>
        </div>
        <div id="education"></div>
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

          for ($i = 1; i <= 9; $i++) {
            if (!is_numeric($_POST["eduYear" + $i])) {
                $_SESSION["error"] = "Year must be numeric";
                header("Location: add.php");
                return false;
            }
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
                    $stmt->execute(array(":lid" => $lastId, ":rank" => $rank, ":ye" => $year, ":de" => $desc));
                  }
      
                  $i++;
                }

                $i = 1;

                while ($i < 10) {
                  if (isset($_POST["year" . $i])) {
                    $year = htmlentities($_POST["year" . $i]);
                    $school = htmlentities($_POST["school" . $i]);

                    $sql = "SELECT institution_id FROM Institution where name like '" . $school . "'";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue(':na', "%{$school}%", PDO::PARAM_STR);
                    $stmt->execute();

                    $iid = "";

                    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                      $iid = $row["institution_id"];

                      if (count($iid) > 0) {
                        break;
                      }
                    }
                    
                    if ($iid == "") {
                      $sql = "INSERT INTO Institution(name) VALUES(:na)";
                      $stmt = $pdo->prepare($sql);
                      $stmt->execute(array(":na" => $school));

                      $iid = $pdo->lastInsertId();
                    }
                    
                    $sql = "INSERT INTO Education(institution_id, profile_id, rank, year) VALUES(:iid, :lid, :rank, :year)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute(
                      array(
                        ":iid" => $iid, 
                        "lid" => $lastId, 
                        ":rank" => $rank, 
                        ":year" => $year)
                      );
                  }
      
                  $i++;
                }

                $rank++;
              
              $_SESSION["added_profile"] = true;
              header("location: index.php");
          } 
        }
      ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>
    <script src="js/add.js"></script>
  </body>
</html>