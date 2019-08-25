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
      <h1>Michael Zech's Resume Registry</h1>
      <?php   
          session_start();

          if (isset($_SESSION['delete_confirmed']) ) {
            echo("<p style='color: green;'>Record deleted</p>\n");
            unset($_SESSION['delete_confirmed']);
          } else if (isset($_SESSION['edited'])) {
            echo("<p style='color: green;'>Record edited</p>\n");
            unset($_SESSION['edited']);
          } else if (isset($_SESSION["added_profile"])) {
            echo("<p style='color: green;'>Record edited</p>\n");
            unset($_SESSION['edited']);
          } else {
            echo "";
          }

          if (!isset($_SESSION["user_id"])) {
              echo "<a href='login.php'>Please log in</a>";
          } else {
              echo "<p><a href='logout.php'>Logout</a></p>";

              require_once "connect.php";

              $sql = "SELECT * FROM Profile";

              $count_rows = $pdo->query($sql)->fetchColumn();

              if ($count_rows > 0) {
                echo "<table border='1'>";
                echo "<tr><th>Name</th><th>Headline</th><th>Action</th></tr>";

                foreach ($pdo->query($sql) as $row) {
                  echo "<tr><td>" . $row["first_name"] . " " . $row["last_name"] 
                    . "</td><td>" . $row["headline"] 
                    . "</td><td><a href='edit.php?profile_id=" 
                      . $row["profile_id"] 
                    . "'>Edit</a><a href='delete.php?profile_id=" 
                      . $row["profile_id"] 
                    . "'> Delete</a></td></tr>";
                }
              
                echo "</table>";
              }

              echo "<p class='anchor-button'><a href='add.php'>Add New Entry</a></p>";
          }
      ?>
    </div>
  </body>
</html>