<!DOCTYPE html>
<html>
  <head>
    <!--
        umsi@umich.edu
    -->
    <meta charset="UTF-8">
    <title>Michael Zech</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css">
    <link rel="stylesheet" href="./css/style.css" type="text/css">
  </head>
  <body>
    <div class="container">      
        <h1>Please Log In</h1>
        <form method="post" action="#" id="login-form">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="text" class="form-control" id="email" name="email" >
            </div>
            <div class="form-group">
                <label for="pass">Password</label>
                <input type="text" class="form-control" id="pass" name="pass" >
            </div>
            <input type="submit" name="login" id="login" class="btn btn-primary" value="Log In" />
            <button type="submit" name="cancel" class="btn btn-warning">Cancel</button>
        </form>
        <?php
            session_start();

            if (isset($_POST["cancel"])) {
                header("location: index.php");
            }

            if (isset($_SESSION["login_fail"])) {
                echo "<script>alert('Invalid email address');</script>";
                unset($_SESSION["login_fail"]);
            } else if (isset($_POST["login"])) {
                $salt = "XyZzy12*_";
                require_once "connect.php";
                
                $check = hash('md5', $salt . $_POST['pass']);
                $stmt = $pdo->prepare('SELECT user_id, name FROM users
                    WHERE email = :em AND password = :pw');
                $stmt->execute(array( ':em' => $_POST['email'], ':pw' => $check));
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if ( $row !== false ) {
                    $_SESSION['name'] = $row['name'];
                    $_SESSION['user_id'] = $row['user_id'];
                    // Redirect the browser to index.php
                    header("Location: index.php");
                    return;
                }
            }
        ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>
    <script src="./js/login.js"></script>
  </body>
</html>