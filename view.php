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
      <h1>Profile information</h1>
        <?php
            require_once "connect.php";

            $stmt = $pdo->prepare(
                'SELECT * 
                FROM Profile 
                WHERE Profile.profile_id = :id');
            $stmt->execute(array(":id" => $_GET["profile_id"]));
            $profile = $stmt->fetch();
            
            $stmt = $pdo->prepare(
                'SELECT * 
                FROM Position 
                WHERE Position.profile_id = :id');
            $stmt->execute(array(":id" => $_GET["profile_id"]));
            $position = $stmt->fetchAll();
            
        ?>
        <div>
            <p>First Name: <?php echo $profile["first_name"]; ?></p>
            <p>Last Name: <?php echo $profile["last_name"]; ?></p>
            <p>Email: <?php echo $profile["email"]; ?></p>
            <p>Headline: <?php echo $profile["headline"]; ?></p>
            <p>Summary: <?php echo $profile["summary"]; ?></p>

            <p>Position:</p>
            <ul>
                <?php
                    foreach($position as $value) {
                        echo "<li>" . $value["year"] . ": " 
                            . $value["description"] . "</li>";
                    }
                ?>
            </ul>
        </div>

        <div>
                <a href="index.php">Done</a>
        </div>
    </div>
  </body>
</html>